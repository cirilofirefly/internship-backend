<?php

namespace App\Http\Controllers\Api\Intern;

use App\Http\Controllers\Controller;
use App\Models\AssignedIntern;
use App\Models\DailyTimeRecord;
use App\Models\Supervisor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function getDashboardCount(Request $request)
    {

        $rendered_time = DailyTimeRecord::where('user_id', $request->user()->id)
            ->where('status', DailyTimeRecord::VALIDATED)
            ->get()
            ->reduce(function($carry, $dailyTimeRecord) {

                $amTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->am_start_time, $dailyTimeRecord->am_end_time);
                $pmTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->pm_start_time, $dailyTimeRecord->pm_end_time);

                return $carry + ($amTotalHours + $pmTotalHours);
            });

        $remaining_time = DailyTimeRecord::TOTAL_HOURS - $rendered_time;
        $absent = 0;

        return response()->json([
            'rendered_time'     => $rendered_time ?? 0,
            'remaining_time'    => $remaining_time,
            'absent'            => $absent,
        ], 200);
    }

    public function getDesignationInfo(Request $request)
    {
        $assignedIntern = AssignedIntern::where('intern_user_id', $request->user()->id)->first();
        return User::where('id', $assignedIntern->supervisor_user_id)
            ->with('supervisor')
            ->first();
    }

    public function getWeeklyAttendance(Request $request)
    {
        return DailyTimeRecord::where('user_id', $request->user()->id)->paginate(5);
    }

    private function calculateTotalHours($date, $start_time, $end_time): float
    {
        $start_time = Carbon::parse($date . ' ' . $start_time);
        $end_time = Carbon::parse($date . ' ' . $end_time);

        return round($end_time->diffInMinutes($start_time, true) / 60, 2);
    }
}
