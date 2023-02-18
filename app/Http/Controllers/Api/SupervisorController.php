<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class SupervisorController extends Controller
{
    public function getAssignedInterns()
    {
        return User::where('id', auth()->user()->id)
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
            ->paginate(5);
    }

}
