<?php

namespace App\Http\Controllers\Api\Intern;

use App\Http\Controllers\Controller;
use App\Models\AssignedIntern;
use App\Models\DailyTimeRecord;
use App\Models\Intern;
use App\Models\OJTCalendar;
use App\Models\Requirement;
use App\Models\Supervisor;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function getDashboardCount(Request $request)
    {

        $user_id = isset($request->user_id) ? 
            $request->user_id : 
            $request->user()->id;

        $rendered_time = DailyTimeRecord::where('user_id', $user_id)
            ->where('status', DailyTimeRecord::VALIDATED)
            ->get()
            ->reduce(function($carry, $dailyTimeRecord) {

                $amTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->am_start_time, $dailyTimeRecord->am_end_time);
                $pmTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->pm_start_time, $dailyTimeRecord->pm_end_time);
                $overtimeTotalHours = 0;
                if(!is_null($dailyTimeRecord->overtime_start_time) || !is_null($dailyTimeRecord->overtime_end_time) ) {
                    $overtimeTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->overtime_start_time, $dailyTimeRecord->overtime_end_time);
                }
                return $carry + (($amTotalHours + $pmTotalHours) + $overtimeTotalHours);
            });

        $remaining_time = DailyTimeRecord::TOTAL_HOURS - $rendered_time;
        $assigned_intern = AssignedIntern::where('intern_user_id', $user_id)->first();
        
        $absent = 0;

        if($assigned_intern) {
            
            $supervisor = Supervisor::where('portal_id', $assigned_intern->supervisor_user_id)->first();

            if(!is_null($supervisor->working_day_start) && !is_null($supervisor->working_day_end)) {

                $ojt_calendars = OJTCalendar::where('supervisor_id', $supervisor->portal_id)
                    ->whereBetween('date', [
                        $supervisor->working_day_start,
                        Carbon::now()->subDay(1)->format('Y-m-d')
                    ])
                    ->where('is_working_day', true)
                    ->get();

                foreach($ojt_calendars as $ojt_calendar) {

                    $dtr = DailyTimeRecord::where('user_id', $user_id)
                        ->where('date', $ojt_calendar->date)
                        ->get();

                    if($dtr->count() === 0) {
                        $absent++;
                    }

                }
            }
        }

        return response()->json([
            $supervisor,
            'rendered_time'     => $rendered_time ?? 0,
            'remaining_time'    => $remaining_time,
            'absent'            => $absent,
        ], 200);
    }

    public function getDesignationInfo(Request $request)
    {
        $assignedIntern = AssignedIntern::where('intern_user_id', $request->user()->id)->first();

        if(!$assignedIntern)
            return null;

        return User::where('id', $assignedIntern->supervisor_user_id)
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

    public function getMonthlyInternDailyTimeRecords(Request $request)
    {
        $intern_ids = AssignedIntern::where('supervisor_user_id', $request->user()->id)
            ->pluck('intern_user_id');

        return DailyTimeRecord::with('intern')
            ->whereIn('user_id', $intern_ids)
            ->whereBetween('date', [ 
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d')
            ])
            ->get();   
    }

    public function getInternRequirements(Request $request)
    {
        $intern_ids = AssignedIntern::where('supervisor_user_id', $request->user()->id)
            ->pluck('intern_user_id');

        return Requirement::with('user')
            ->whereIn('user_id', $intern_ids)
            ->get();  
    }

    public function internshipStats(Request $request)
    {

        $coordinator_id = $request->user()->id;

        $ojt_count = User::whereIntern()
            ->whereRelation('intern', 'coordinator_id', $coordinator_id)
            ->where('status', User::APPROVED)
            ->count();

        $office_count = User::whereSupervisor()
            ->whereRelation('supervisor', 'coordinator_id', $coordinator_id)
            ->where('status', User::APPROVED)
            ->count();

        $intern_requirement_submitted = 0;
        $intern_requirement_did_not_submit = 0;

        $interns = Intern::where('coordinator_id', $coordinator_id)
            ->get()->pluck(['portal_id']);
        
        foreach($interns as $intern) {

            $hasSubmitted = DailyTimeRecord::where('user_id', $intern)
                ->whereRelation('detailedReport', 'status', 'submitted')
                ->exists();
            
            if($hasSubmitted) {
                $intern_requirement_submitted++;
            } else {
                $intern_requirement_did_not_submit++;
            }
        }

        return response()->json([
            'ojt_count'                         => $ojt_count,
            'office_count'                      => $office_count,
            'intern_requirement_submitted'      => $intern_requirement_submitted,
            'intern_requirement_did_not_submit' => $intern_requirement_did_not_submit,
        ]);
    }
}
