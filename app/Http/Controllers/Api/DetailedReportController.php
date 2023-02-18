<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTimeRecordRequest;
use App\Http\Requests\DetailedReportRequest;
use App\Models\AssignedIntern;
use App\Models\DailyTimeRecord;
use App\Models\DetailedReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailedReportController extends Controller
{
    public function getDetailedReports(Request $request)
    {
        return collect(DailyTimeRecord::where('user_id', $request->user()->id)
            ->select('daily_time_records.*', DB::raw("DATE_FORMAT(date, '%m-%Y') monthyear"))
            ->with('detailedReport')
            ->orderBy('date', 'DESC')
            ->get())
            ->groupBy('monthyear')
            ->all();
        
    }

    public function submitDetailedReport(Request $request)
    {
        return DetailedReport::whereIn('id', $request->ids)
            ->update(['status' => 'submitted']);
    }

    public function saveDetailedReport(DetailedReportRequest $request)
    {
        
        return DetailedReport::updateOrCreate([
            'daily_time_record_id' => $request->daily_time_record_id
        ], $request->validated());
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
            ->paginate(5);
    }

}
