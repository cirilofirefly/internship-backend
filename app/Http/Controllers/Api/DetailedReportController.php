<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTimeRecordRequest;
use App\Http\Requests\DetailedReportRequest;
use App\Models\DailyTimeRecord;
use App\Models\DetailedReport;
use Illuminate\Http\Request;

class DetailedReportController extends Controller
{
    public function getDetailedReports(Request $request)
    {
        return DailyTimeRecord::where('user_id', $request->user()->id)
            ->with('detailedReport')
            ->orderBy('date', 'ASC')
            ->paginate(5);
    }

    public function saveDetailedReport(DetailedReportRequest $request)
    {
        
        return DetailedReport::updateOrCreate([
            'daily_time_record_id' => $request->daily_time_record_id
        ], $request->validated());
    }

}
