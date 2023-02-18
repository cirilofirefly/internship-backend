<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTimeRecordRequest;
use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTimeRecordController extends Controller
{
    public function getDailyTimeRecords(Request $request)
    {
        return collect(DailyTimeRecord::where('user_id', $request->user()->id)
            ->select('daily_time_records.*', DB::raw("DATE_FORMAT(date, '%m-%Y') monthyear"))
            ->orderBy('date', 'desc')
            ->get())
            ->groupBy('monthyear')
            ->all();
    }

    public function submitDailyTimeRecord(Request $request)
    {
        return DailyTimeRecord::whereIn('id', $request->ids)
            ->update(['status' => 'submitted']);
    }

    public function saveDailyTimeRecord(DailyTimeRecordRequest $request)
    {
        $request->merge(['user_id' => $request->user()->id]);
        return DailyTimeRecord::create($request->except('monthyear'));
    }

    public function updateDailyTimeRecord($id, DailyTimeRecordRequest $request)
    {
        return DailyTimeRecord::where('id', $id)
            ->update($request->except('monthyear', 'created_at', 'updated_at'));
    }

    public function deleteDailyTimeRecord($id)
    {
        return DailyTimeRecord::destroy($id);
    }
}
