<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTimeRecordRequest;
use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTimeRecordController extends Controller
{

    public function timeInOut(Request $request)
    {
        $user = User::select(
                'users.id', 
                'users.first_name', 
                'users.last_name', 
                'interns.student_number as student_number'
            )
            ->whereIntern()
            ->leftJoin('interns', 'users.id', '=', 'interns.portal_id')
            ->where('card_id', base64_encode($request->cardId))
            ->first();

        if ($user) {
            $this->checkDTRCount($user);
            return response()->json($user, 200);
        }

        return response()->json(['message' => 'Card not recognized'], 404);
    }

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
            ->where('status', 'default')
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

    private function checkDTRCount(User $user)
    {
        $user->increment('dtr_time_count', 1);

        switch($user->dtr_time_count) {
            case 4:
                $user->dtr_time_count = 0;
                $user->save();

                break;
        }
    }
}
