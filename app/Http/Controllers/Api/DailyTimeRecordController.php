<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTimeRecordRequest;
use App\Models\AssignedIntern;
use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTimeRecordController extends Controller
{

    public function getInternSupervisor(Request $request)
    {
        $assigned_intern = AssignedIntern::where('intern_user_id', $request->user()->id)->first();
        return $assigned_intern->supervisor;
    }

    public function timeInOut(Request $request)
    {
        $user = User::select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.dtr_time_count',
                'interns.student_number as student_number'
            )
            ->whereIntern()
            ->leftJoin('interns', 'users.id', '=', 'interns.portal_id')
            ->where('card_id', base64_encode($request->card_id))
            ->first();

        if ($user) {
            return response()->json($this->checkDTRCount($user), 200);
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

        $checkName = 'default';

        $dailyTimeRecord = DailyTimeRecord::whereDate('date', \Carbon\Carbon::now())
            ->where('user_id', $user->id)->first();

        if(!$dailyTimeRecord) {
            $dailyTimeRecord = DailyTimeRecord::create([
                'date'                  => \Carbon\Carbon::now()->toDateString(),
                'am_start_time'         => '',
                'am_end_time'           => '',
                'pm_start_time'         => '',
                'pm_end_time'           => '',
                'overtime_start_time'   => '',
                'overtime_end_time'     => '',
                'description'           => '',
                'status'                => 'default',
                'user_id'               => $user->id
            ]);
        }

        $currentTime = \Carbon\Carbon::now()->format('H:i');

        if($user->dtr_time_count >= 4) {

            $dailyTimeRecord->pm_end_time = $currentTime;
            $dailyTimeRecord->save();
            $checkName = 'PM Time Out';

            $user->dtr_time_count = 0;
            $user->save();

            return $this->checkInReponse($user, $currentTime, $checkName);

        } else {

            switch($user->dtr_time_count) {
                case 1:
                    $checkName = 'AM Time In';
                    $dailyTimeRecord->am_start_time = $currentTime;
                    break;
                case 2:
                    $checkName = 'AM Time Out';
                    $dailyTimeRecord->am_end_time = $currentTime;
                    break;
                case 3:
                    $checkName = 'PM Time In';
                    $dailyTimeRecord->pm_start_time = $currentTime;
                    break;
            }

            $dailyTimeRecord->save();
        }

        return $this->checkInReponse($user, $currentTime, $checkName);
    }

    private function checkInReponse($user, $currentTime, $checkName)
    {
        return [
            'user'          => $user,
            'current_time'  => $currentTime,
            'check_name'    => $checkName
        ];
    }
}
