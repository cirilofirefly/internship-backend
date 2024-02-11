<?php

namespace App\Console\Commands;

use App\Models\OJTCalendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateCalendarWorkingDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:calendar-working-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Calendar Working Day in every supervisor.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::select('id')
            ->with('supervisor')
            ->whereSupervisor()
            ->get();

        $current_year = date('Y');
        $totalMonths = 12;

        foreach($users as $user) {

            for($month = 1; $month <= $totalMonths; $month++) {

                $total_days_in_a_month = cal_days_in_month(CAL_GREGORIAN, $month, $current_year);

                for($day = 1; $day <= $total_days_in_a_month; $day++) {

                    $formatted_date = Carbon::createFromFormat('Y-m-d',  $current_year . '-' . $month . '-' . $day);
                    $note = $formatted_date->isWeekend() ? 'Weekend' : 'Working Day';

                    OJTCalendar::firstOrCreate(
                        [
                            'supervisor_id'  => $user->id,
                            'date'           => $formatted_date->format('Y-m-d'),
                        ],
                        [
                            'title'          => $note,
                            'note'           => $note,
                            'is_working_day' => !$formatted_date->isWeekend(),
                        ]
                    );

                }
            }
        };
    }
}
