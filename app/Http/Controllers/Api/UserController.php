<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupervisorRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UserRequest;
use App\Mail\CreatedSupervisor;
use App\Models\AssignedIntern;
use App\Models\Coordinator;
use App\Models\Supervisor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Str;

class UserController extends Controller
{
    public function availableCoordinator(Request $request)
    {
        return User::where('user_type', User::COORDINATOR)
            ->whereRelation('coordinator', 'program', '=', $request->program)
            ->get();
    }

    public function saveUser(UserRequest $request)
    {
        return User::updateOrCreate(
            [
                'id' => $request->user_id
            ],
            $request->except(
                'user_id',
                'password_confirmation',
                'edit_mode'
            )
        );
    }

    public function createSupervisor(SupervisorRequest $request)
    {
        $password = Str::random(12);

        try {

            DB::beginTransaction();

            $user = User::create([
                'username'          => $request->username,
                'email'             => $request->email,
                'first_name'        => $request->first_name,
                'last_name'         => $request->last_name,
                'password'          => $password,
                'middle_name'       => 'N/A',
                'suffix'            => 'N/A',
                'contact_number'    => 'N/A',
                'birthday'          => Carbon::now()->format('Y-m-d'),
                'gender'            => 'others',
                'nationality'       => 'N/A',
                'civil_status'      => 'N/A',
                'status'            => User::APPROVED,
                'user_type'         => User::SUPERVISOR,
            ]);

            Supervisor::create([
                'host_establishment'    => $request->host_establishment,
                'designation'           => $request->designation,
                'campus_type'           => $request->campus_type,
                'portal_id'             => $user->id,
                'coordinator_id'        => $request->user()->id
            ]);

            DB::commit();

            Mail::to($user->email)->queue(new CreatedSupervisor($password, $user));

            return response()->json($user, 200);

        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['Creating supervisor failed: ' . $e->getMessage()], 500);
        }

    }

    public function updateSupervisor(SupervisorRequest $request)
    {
        $user = User::where('id', $request->id)->update([
            'username'          => $request->username,
            'email'             => $request->email,
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'middle_name'       => $request->middle_name,
            'suffix'            => $request->suffix,
            'contact_number'    => $request->contact_number,
            'birthday'          => $request->birthday,
            'gender'            => $request->gender,
            'nationality'       => $request->nationality,
            'civil_status'      => $request->civil_status,
        ]);

        Supervisor::where('portal_id', $request->id)
            ->update([
                'host_establishment'    => $request->host_establishment,
                'designation'           => $request->designation,
                'campus_type'           => $request->campus_type,
            ]);

        return response()->json($user, 200);
    }

    public function getSupervisors(Request $request)
    {
        return response()->json(
            User::whereSupervisor()
                ->with('supervisor')
                ->whereRelation('supervisor', 'coordinator_id', $request->user()->id)
                ->where(function($query) use($request) {
                    if($request->status != 'null')
                        return $query->where('status', $request->status);
                })
                ->paginate(5)
        );
    }

    public function getSupervisor($id)
    {
        return User::whereSupervisor()
            ->where('id', $id)
            ->first();
    }

    public function getInterns(Request $request)
    {
        $searchKeyword = isset($request->search) ? $request->search : '';
        $assign_filter = $request->assign_filter;

        $interns = User::whereIntern()
                ->when($request->status !== 'null', function($q) use($request) {
                    return $q->where('status', $request->status);
                })
                ->where(function($query) use($searchKeyword) {
                    $query->where('first_name', 'LIKE', "%{$searchKeyword}%")
                        ->orWhere('middle_name', 'LIKE', "%{$searchKeyword}%")
                        ->orWhere('last_name' , 'LIKE', "%{$searchKeyword}%");
                })
                ->with(['intern' => function($query) use($searchKeyword) {
                    $query->orWhere('student_number', 'LIKE', "%{$searchKeyword}%");
                }])
                ->when($assign_filter !== 'ALL', function($q) use($assign_filter) {

                    if($assign_filter == 'assigned') {
                        return $q->has('assignedIntern');
                    }

                    return $q->doesntHave('assignedIntern');
                })
                ->when($request->year != 'ALL', function($query) use($request) {
                    $query->whereYear('created_at', $request->year);
                })
                ->whereRelation('intern', 'coordinator_id', $request->user()->id);

        return response()->json($interns->paginate(10));
    }

    public function approveIntern(Request $request)
    {
        return User::whereIntern()
            ->whereUserId($request->id)
            ->update(['status' => User::APPROVED]);
    }

    public function declineIntern(Request $request)
    {
        return User::whereIntern()
            ->whereUserId($request->id)
            ->update(['status' => User::DECLINED]);
    }


    public function getIntern($id)
    {
        return User::whereIntern()
            ->where('id', $id)
            ->first();
    }

    public function getUser($id)
    {
        return User::where('id', $id)
            ->first();
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::where('id', $request->id)->first();
        if($user) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->middle_name = $request->middle_name;
            $user->suffix = $request->suffix;
            $user->birthday = $request->birthday;
            $user->civil_status = $request->civil_status;
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->save();
        }
    }

    public function changeProfilePicture(Request $request)
    {
        if($request->file('profile_picture')) {
            User::where('id', auth()->user()->id)
                ->update([
                    'profile_picture' => $request->file('profile_picture')->store('profile_pictures')
                ]);
        }
    }

    public function changeESignature(Request $request)
    {
        if($request->file('e_signature')) {
            User::where('id', auth()->user()->id)
                ->update([
                    'e_signature' => $request->file('e_signature')->store('e_signatures')
                ]);
        }
    }

    public function getProfileInfo(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();
        return response()->json([
            'user'              => $user,
            $user->user_type    => $this->getProfileDataByUserType($user)
        ]);
    }

    public function uploadProfilePicture(Request $request)
    {
        if($request->file('profile_picture')) {
            $file = $request->profile_picture->store('profile_pictures');
            User::where('id', $request->user()->id)->update(['profile_picture' => $file]);

            if(isset($request->profile_picture_path)) {
                $this->deleteFileIfExist($request->profile_picture_path);
            }

            return response(['message' => 'Profile Picture uploaded.', 'profile_picture' => $file], 200);
        }
        return response()->json(['message' => 'Profile Picture is required.'], 422);
    }

    public function uploadESignature(Request $request)
    {

        if($request->file('e_signature')) {
            $file = $request->e_signature->store('e_signatures');
            User::where('id', $request->user()->id)->update(['e_signature' => $file]);

            if(isset($request->e_signature_path)) {
                $this->deleteFileIfExist($request->e_signature_path);
            }

            return response(['message' => 'E Signature uploaded.', 'e_signature' => $file], 200);
        }
        return response()->json(['message' => 'E Signature is required.'], 422);
    }

    private function getProfileDataByUserType($user)
    {

        if($user->user_type == User::COORDINATOR)
            return Coordinator::where('portal_id', $user->id)->first();
        if($user->user_type == User::SUPERVISOR)
            return Supervisor::where('portal_id', $user->id)->first();
    }
}
