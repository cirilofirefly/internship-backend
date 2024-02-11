<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignInternRequest;
use App\Models\AssignedIntern;
use App\Models\DailyTimeRecord;
use App\Models\InternJobPreference;
use App\Models\Requirement;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;

class CoordinatorController extends Controller
{

    public function internRfidRegistration(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if($user) {
            $user->card_id = base64_encode($request->cardId);
            $user->save();
            return response()->json(['success' => true], 200);
        }

        return response()->json(['message' => 'User not found.'], 404);
    }

    public function getOffices()
    {
        return User::whereSupervisor()
            ->select('id', 'first_name', 'last_name', 'middle_name')
            ->with([
                'supervisor',
                'assignedInterns' => function($query) {
                    $query->with('intern', function($query) {
                        $query->select('id', 'username', 'first_name', 'last_name', 'middle_name', 'email')
                            ->with('intern');
                    });
                }
            ])
            ->whereRelation('supervisor', 'coordinator_id', auth()->user()->id)
            ->paginate(5);
    }

    public function assignIntern(AssignInternRequest $request)
    {
        $assigned_intern = AssignedIntern::updateOrCreate([
            'intern_user_id'        => $request->intern_user_id,
            'supervisor_user_id'    => $request->supervisor_user_id,
        ], $request->validated());
        return $assigned_intern->intern->intern->update(['is_assigned' => true]);
    }

    public function getApprovedInterns(Request $request)
    {
        $onlyNotAssigned = isset($request->onlyNotAssigned);
        $assigned_intern_ids = [];

        if($onlyNotAssigned) {
            $assigned_intern_ids = AssignedIntern::distinct()
                ->get()
                ->pluck('intern_user_id');
        }

        return User::whereIntern()
            ->approved()
            ->with('intern')
            ->whereRelation('intern', 'coordinator_id', auth()->user()->id)
            ->whereNotIn('id', $assigned_intern_ids)
            ->get();
    }

    public function getAssignedInterns(Request $request)
    {
        return AssignedIntern::with(['intern' => function($query) use($request) {
                $query
                    ->with('intern')
                    ->whereHas('intern', function($query) use($request) {
                    $query->where('coordinator_id', $request->user()->id);
                });
            }])
            ->get();
    }

    public function validateRequirments(Request $request)
    {
        return Requirement::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    public function getInternEvaluation(Request $request)
    {
        $intern_job_preference = InternJobPreference::where('intern_user_id', $request->user_id)
            ->first();
        return response()->json($intern_job_preference, 200);
    }

    public function getNoSubmitStudents(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        switch($request->submission_type) {
            case 'detailed-report':
                return $this->detailedReportNoSubmission($user, $search);
            case 'daily-time-record':
                return $this->dailyTimeRecordNoSubmission($user, $search);
        }

    }

    public function getInternEvaluationStatus(Request $request)
    {
        $supervisorUserIds = Supervisor::where('coordinator_id', $request->user()->id)->pluck('portal_id');
        $assigned_interns = AssignedIntern::whereIn('supervisor_user_id', $supervisorUserIds)
            ->with('intern')
            ->whereRelation('intern', function($query) use($request) {
                $query->when(isset($request->search), function($query) use($request) {
                    $query->whereRaw("concat(first_name, ' ', last_name) like '%$request->search%' ")
                        ->orWhere('username', 'LIKE', "%{$request->search}%");
                });
            })
            ->get();

        $assigned_interns = $assigned_interns->map(function($assigned_intern) {
            $assigned_intern['is_evaluated'] = InternJobPreference::where('intern_user_id', $assigned_intern->intern_user_id)->exists();
            return $assigned_intern;
        });

        if($request->status !== 'ALL') {
            $assigned_interns = $assigned_interns->filter(function($assigned_intern) use($request) {
                $evaluationStatus = $request->status == 'evaluated';
                return $assigned_intern['is_evaluated'] === $evaluationStatus;
            });
        }

        return $assigned_interns;
    }

    private function detailedReportNoSubmission($user, $search)
    {
        $userIds = User::whereSupervisor()
                ->whereRelation('supervisor', 'coordinator_id', $user->id)
                ->pluck('id');

        $assigned_interns = AssignedIntern::whereIn('supervisor_user_id', $userIds)
            ->with('intern')
            ->whereRelation('intern', function($query) use($search) {
                $query->when($search, function($query) use($search) {
                    $query->whereRaw("concat(first_name, ' ', last_name) like '%$search%' ")
                        ->orWhere('username', 'LIKE', "%{$search}%");
                });
            })
            ->get();

        return $assigned_interns->filter(function($assigned_intern) {

            return DailyTimeRecord::where('user_id', $assigned_intern->intern_user_id)
                ->with('detailedReport')
                ->whereRelation('detailedReport', function($query) {
                    $query->whereIn('status', ['submitted', 'validated']);
                })
                ->count() === 0;
        });
    }

    private function dailyTimeRecordNoSubmission($user, $search)
    {
        $userIds = User::whereSupervisor()
                ->whereRelation('supervisor', 'coordinator_id', $user->id)
                ->pluck('id');

        $assigned_interns = AssignedIntern::whereIn('supervisor_user_id', $userIds)
            ->with('dailyTimeRecords', 'intern')
            ->whereRelation('intern', function($query) use($search) {
                $query->when($search, function($query) use($search) {
                    $query->whereRaw("concat(first_name, ' ', last_name) like '%$search%' ")
                        ->orWhere('username', 'LIKE', "%{$search}%");
                });
            })
            ->get();

        return $assigned_interns->filter(function($assigned_intern) {
            $hasSubmission = count($assigned_intern->dailyTimeRecords->filter(function($dailyTimeRecord) {
                return in_array($dailyTimeRecord->status, ['validated', 'submitted']);
            })) > 0;
            return count($assigned_intern->dailyTimeRecords) == 0 || !$hasSubmission;
        });
    }
}
