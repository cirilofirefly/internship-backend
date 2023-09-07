<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignInternRequest;
use App\Models\AssignedIntern;
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
            $assigned_intern_ids = AssignedIntern::all()
                ->distinct()
                ->pluck('intern_user_id');
        }

        return User::whereIntern()
            ->approved()
            ->with('intern')
            ->whereRelation('intern', 'coordinator_id', auth()->user()->id)
            ->whereNotIn('id', $assigned_intern_ids)
            ->get();
    }

    public function getAssignedInterns()
    {
        return AssignedIntern::with('intern')->get();
    }
}
