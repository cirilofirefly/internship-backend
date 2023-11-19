<?php

namespace App\Console\Commands;

use App\Models\OJTCalendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

                    OJTCalendar::create([
                        'title'          => $note,
                        'date'           => $formatted_date, 
                        'note'           => $note,
                        'is_working_day' => !$formatted_date->isWeekend(),
                        'supervisor_id'  => $user->id,
                    ]);
                
                }
            }
        };
    }
}
