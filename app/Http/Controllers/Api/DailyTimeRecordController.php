<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTimeRecordRequest;
use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;

class DailyTimeRecordController extends Controller
{
    public function getDailyTimeRecords(Request $request)
    {
        return DailyTimeRecord::where('user_id', $request->user()->id)
            ->orderBy('date', 'ASC')
            ->paginate(5);
    }

    public function saveDailyTimeRecord(DailyTimeRecordRequest $request)
    {
        $request->merge(['user_id' => $request->user()->id]);
        return DailyTimeRecord::create($request->all());
    }

    public function updateDailyTimeRecord($id, DailyTimeRecordRequest $request)
    {
        return DailyTimeRecord::where('id', $id)
            ->update($request->except('created_at', 'updated_at'));
    }

    public function deleteDailyTimeRecord($id)
    {
        return DailyTimeRecord::destroy($id);
    }
}
