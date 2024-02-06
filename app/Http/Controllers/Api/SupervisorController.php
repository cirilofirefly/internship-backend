<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkingDayPeriodRequest;
use App\Models\AssignedIntern;
use App\Models\DailyTimeRecord;
use App\Models\DetailedReport;
use App\Models\InternJobPreference;
use App\Models\OJTCalendar;
use App\Models\Requirement;
use App\Models\Supervisor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    public function getAssignedInterns(Request $request)
    {
        $no_paginate = isset($request->no_paginate) ? $request->no_paginate : false;

        $users = User::where('id', auth()->user()->id)
            ->select('id', 'first_name', 'last_name', 'middle_name')
            ->with([
                'supervisor',
                'assignedInterns' => function($query) use($request) {
                    $query->with('intern', function($query) use($request) {
                        $query->with('intern');
                    })
                    ->when(isset($request->search), function($query) use($request) {
                        $query->whereRelation('intern', 'username', 'LIKE', "%{$request->search}%");
                    });
                }
            ]);

        return $no_paginate ? $users->get() : $users->paginate(5);
    }

    public function getInternDailyTimeRecords(Request $request)
    {
        $canDateRangeFilter = (isset($request->start_date) && isset($request->end_date)) && ($this->dateFormat($request->start_date)->lte($this->dateFormat($request->end_date)));

        return collect(DailyTimeRecord::where('user_id', $request->user_id)
            ->where(function($query) {
                $query->where('status', 'submitted')
                    ->orWhere('status', 'validated');
            })
            ->select('daily_time_records.*', DB::raw("DATE_FORMAT(date, '%m-%Y') monthyear"))
            ->orderBy('date', 'DESC')
            ->when($canDateRangeFilter, function($query) use($request) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            })
            ->get())
            ->groupBy('monthyear')
            ->all();
    }

    public function getInternDetailedReports(Request $request)
    {
        $canDateRangeFilter = (isset($request->start_date) && isset($request->end_date)) && ($this->dateFormat($request->start_date)->lte($this->dateFormat($request->end_date)));

        return collect(DailyTimeRecord::where('user_id', $request->user_id)
            ->with('detailedReport')
            ->whereRelation('detailedReport', function($query) {
                $query->where('status', 'submitted')
                    ->orWhere('status', 'validated');

            })
            ->select('daily_time_records.*', DB::raw("DATE_FORMAT(date, '%m-%Y') monthyear"))
            ->orderBy('date', 'DESC')
            ->when($canDateRangeFilter, function($query) use($request) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            })
            ->get())
            ->groupBy('monthyear')
            ->all();
    }

    public function validateInternDailyTimeRecords(Request $request)
    {
        return DailyTimeRecord::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    public function validateRequirments(Request $request)
    {
        return Requirement::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    public function validateInternDetailedReports(Request $request)
    {
        return DetailedReport::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    public function saveInternEvaluation(Request $request)
    {
        InternJobPreference::updateOrCreate(
            [
                'evaluator_user_id' => $request->user()->id,
                'intern_user_id'    => $request->user_id,
            ],
            [
                'evaluator_user_id' => $request->user()->id,
                'intern_user_id'    => $request->user_id,
                'evaluation'        => $request->evaluation,
            ]
        );

        return response()->json(['message' => 'Evaluation saved.']);
    }

    public function getInternEvaluation(Request $request)
    {
        $rendered_time = DailyTimeRecord::where('user_id', $request->user_id)
            ->where('status', DailyTimeRecord::VALIDATED)
            ->get()
            ->reduce(function($carry, $dailyTimeRecord) {

                $amTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->am_start_time, $dailyTimeRecord->am_end_time);
                $pmTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->pm_start_time, $dailyTimeRecord->pm_end_time);
                $overtimeTotalHours = 0;

                if(!is_null($dailyTimeRecord->overtime_start_time) && !is_null($dailyTimeRecord->overtime_end_time) ) {
                    $overtimeTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->overtime_start_time, $dailyTimeRecord->overtime_end_time);
                }

                return $carry + (($amTotalHours + $pmTotalHours) + $overtimeTotalHours);
            });

        $evaluation = InternJobPreference::where('evaluator_user_id', $request->user()->id)
            ->where('intern_user_id', $request->user_id)
            ->first();

        return response()->json([
            'evaluation'    => $evaluation,
            'can_evaluate'  => $rendered_time >= DailyTimeRecord::TOTAL_HOURS
        ]);
    }

    public function getOJTWorkingDays(Request $request)
    {

        $has_working_period = !is_null($request->user()->supervisor->working_day_start) && !is_null($request->user()->supervisor->working_day_end);

        return response()->json([
            'has_working_period'    => $has_working_period,
            'ojt_calendar'          => OJTCalendar::whereBetween('date',[$request->start, $request->end])
                ->where('supervisor_id', $request->user()->id)
                ->get()
        ]);
    }

    public function updateOJTWorkingDay(Request $request)
    {
        return OJTCalendar::where('id', $request->id)
            ->update([
                'title'          => $request->is_working_day ? 'Working Day' : 'Non-working day',
                'note'           => $request->note,
                'is_working_day' => $request->is_working_day
            ]);
    }

    public function updateWorkingPeriod(WorkingDayPeriodRequest $request)
    {
        return Supervisor::where('portal_id', $request->user()->id)->update([
            'working_day_start' => $request->start,
            'working_day_end'   => $request->end,
        ]);
    }

    public function getNoSubmitStudents(Request $request)
    {
        $user = $request->user();
        switch($request->submission_type) {
            case 'detailed-report':
                return $this->detailedReportNoSubmission($user);
            case 'daily-time-record':
                return $this->dailyTimeRecordNoSubmission($user);
        }
    }

    private function calculateTotalHours($date, $start_time, $end_time): float
    {
        $start_time = Carbon::parse($date . ' ' . $start_time);
        $end_time = Carbon::parse($date . ' ' . $end_time);

        return round($end_time->diffInMinutes($start_time, true) / 60, 2);
    }


    private function dateFormat($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    private function detailedReportNoSubmission($user)
    {
        $assigned_interns = AssignedIntern::where('supervisor_user_id', $user->id)
            ->with('intern')
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

    public function getInternEvaluationStatus(Request $request)
    {
        $assigned_interns = AssignedIntern::where('supervisor_user_id', $request->user()->id)
            ->with('intern')
            ->whereRelation('intern', function($query) use($request) {
                $query->where('username', 'like', '%' . $request->search . '%');
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

    private function dailyTimeRecordNoSubmission($user)
    {
        $assigned_interns = AssignedIntern::where('supervisor_user_id', $user->id)
            ->with('dailyTimeRecords', 'intern')
            ->get();

        return $assigned_interns->filter(function($assigned_intern) {
            $hasSubmission = count($assigned_intern->dailyTimeRecords->filter(function($dailyTimeRecord) {
                return in_array($dailyTimeRecord->status, ['validated', 'submitted']);
            })) > 0;
            return count($assigned_intern->dailyTimeRecords) == 0 || !$hasSubmission;
        });
    }

}
