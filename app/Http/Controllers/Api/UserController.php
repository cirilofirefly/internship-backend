<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UserRequest;
use App\Models\Coordinator;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function availableCoordinator()
    {
        return User::where('user_type', User::COORDINATOR)->get();
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

    public function getInterns(Request $request)
    {
        return response()->json(
            User::whereIntern()
                ->with('intern', function($query) use($request) {
                    $query->where('coordinator_id', $request->user()->id);
                })
                ->where(function($query) use($request) {
                    if($request->status != 'null')
                        return $query->where('status', $request->status);
                })
                ->paginate(5)
        );
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
        $user = User::where('id', $request->user_id)->first();
        return response()->json([
            'user'              => $user,
            $user->user_type    => $this->getProfileDataByUserType($user)
        ]);
    }

    public function uploadProfilePicture(Request $request)
    {
        if($request->file('profile_picture')) {
            $file = $request->profile_picture->store('profile_pictures');
            User::where('id', $request->user_id)->update(['profile_picture' => $file]);

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
            User::where('id', $request->user_id)->update(['e_signature' => $file]);

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
