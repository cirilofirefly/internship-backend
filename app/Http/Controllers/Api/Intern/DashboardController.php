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
        $supervisor_id = 0;
        $assignedIntern = AssignedIntern::where('intern_user_id', $request->user()->id)->first();
        if(!$assignedIntern)
            return null;

        return User::where('id', $supervisor_id)
            ->with('supervisor')
            ->first();
    }

    public function getWeeklyAttendance(Request $request)
    {
        return DailyTimeRecord::where('user_id', $request->user()->id)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->paginate(5);
    }

    private function calculateTotalHours($date, $start_time, $end_time): float
    {
        $start_time = Carbon::parse($date . ' ' . $start_time);
        $end_time = Carbon::parse($date . ' ' . $end_time);

        return round($end_time->diffInMinutes($start_time, true) / 60, 2);
    }

    public function getTodayInternDailyTimeRecords(Request $request)
    {
        $intern_ids = AssignedIntern::where('supervisor_user_id', $request->user()->id)
            ->pluck('intern_user_id');

        return DailyTimeRecord::with('intern')
            ->whereIn('user_id', $intern_ids)
            ->whereDate('created_at', Carbon::today())
            ->get();   
    }

    public function internshipStats()
    {
        $ojt_count = User::whereIntern()
            ->where('status', User::APPROVED)
            ->count();


        $office_count = User::whereSupervisor()
            ->where('status', User::APPROVED)
            ->count();
            
        return response()->json([
            'ojt_count' => $ojt_count,
            'office_count' => $office_count

        ]);
    }
}
