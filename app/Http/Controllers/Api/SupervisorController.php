<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyTimeRecord;
use App\Models\DetailedReport;
use App\Models\User;
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
                'assignedInterns' => function($query) {
                    $query->with('intern', function($query) {
                        $query->select('id', 'username', 'first_name', 'last_name', 'middle_name', 'email')
                            ->with('intern');
                    });
                }
            ]);
        return $no_paginate ? $users->get() : $users->paginate(5);
    }

    public function getInternDailyTimeRecords(Request $request)
    {
        return collect(DailyTimeRecord::where('user_id', $request->user_id)
            ->where(function($query) {
                $query->where('status', 'submitted')
                    ->orWhere('status', 'validated');
            })
            ->select('daily_time_records.*', DB::raw("DATE_FORMAT(date, '%m-%Y') monthyear"))
            ->orderBy('date', 'DESC')
            ->get())
            ->groupBy('monthyear')
            ->all();
    }

    public function getInternDetailedReports(Request $request)
    {
        return collect(DailyTimeRecord::where('user_id', $request->user_id)
            ->with('detailedReport')
            ->whereRelation('detailedReport', function($query) {
                $query->where('status', 'submitted')
                    ->orWhere('status', 'validated');

            })
            ->select('daily_time_records.*', DB::raw("DATE_FORMAT(date, '%m-%Y') monthyear"))
            ->orderBy('date', 'DESC')
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

    public function validateInternDetailedReports(Request $request) 
    {
        return DetailedReport::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    
    

}
