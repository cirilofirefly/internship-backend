<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkingDayPeriodRequest;
use App\Models\DailyTimeRecord;
use App\Models\DetailedReport;
use App\Models\InternJobPreference;
use App\Models\OJTCalendar;
use App\Models\Requirement;
use App\Models\Supervisor;
use App\Models\User;
use Carbon\Carbon;
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
                        $query->with('intern');
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

    public function validateRequirments(Request $request) 
    {
        return Requirement::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    public function validateInternDetailedReports(Request $request) 
    {
        return DetailedReport::whereIn('id', $request->ids)
            ->where('status', 'submitted')
            ->update(['status' => 'validated']);
    }

    public function saveInternEvaluation(Request $request)
    {
        InternJobPreference::updateOrCreate(
            [
                'evaluator_user_id' => $request->user()->id,
                'intern_user_id'    => $request->user_id,
            ],
            [
                'evaluator_user_id' => $request->user()->id,
                'intern_user_id'    => $request->user_id,
                'evaluation'        => $request->evaluation,
            ]
        );
        
        return response()->json(['message' => 'Evaluation saved.']);
    }

    public function getInternEvaluation(Request $request)
    {
        $rendered_time = DailyTimeRecord::where('user_id', $request->user_id)
            ->where('status', DailyTimeRecord::VALIDATED)
            ->get()
            ->reduce(function($carry, $dailyTimeRecord) {

                $amTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->am_start_time, $dailyTimeRecord->am_end_time);
                $pmTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->pm_start_time, $dailyTimeRecord->pm_end_time);
                $overtimeTotalHours = 0;

                if(!is_null($dailyTimeRecord->overtime_start_time) && !is_null($dailyTimeRecord->overtime_end_time) ) {
                    $overtimeTotalHours = $this->calculateTotalHours($dailyTimeRecord->date, $dailyTimeRecord->overtime_start_time, $dailyTimeRecord->overtime_end_time);
                }
                
                return $carry + (($amTotalHours + $pmTotalHours) + $overtimeTotalHours);
            });

        $evaluation = InternJobPreference::where('evaluator_user_id', $request->user()->id)
            ->where('intern_user_id', $request->user_id)
            ->first();

        return response()->json([
            'evaluation'    => $evaluation,
            'can_evaluate'  => $rendered_time >= DailyTimeRecord::TOTAL_HOURS
        ]);
    }

    public function getOJTWorkingDays(Request $request)
    {

        $has_working_period = !is_null($request->user()->supervisor->working_day_start) && !is_null($request->user()->supervisor->working_day_end);

        return response()->json([
            'has_working_period'    => $has_working_period,
            'ojt_calendar'          => OJTCalendar::whereBetween('date',[$request->start, $request->end])
                ->where('supervisor_id', $request->user()->id)
                ->get()
        ]);
    }

    public function updateOJTWorkingDay(Request $request)
    {
        return OJTCalendar::where('id', $request->id)
            ->update([
                'title'          => $request->is_working_day ? 'Working Day' : 'Non-working day',
                'note'           => $request->note,
                'is_working_day' => $request->is_working_day
            ]);
    }

    public function updateWorkingPeriod(WorkingDayPeriodRequest $request)
    {
        return Supervisor::where('portal_id', $request->user()->id)->update([
            'working_day_start' => $request->start,
            'working_day_end'   => $request->end,
        ]);
    }

    private function calculateTotalHours($date, $start_time, $end_time): float
    {
        $start_time = Carbon::parse($date . ' ' . $start_time);
        $end_time = Carbon::parse($date . ' ' . $end_time);

        return round($end_time->diffInMinutes($start_time, true) / 60, 2);
    }

}
