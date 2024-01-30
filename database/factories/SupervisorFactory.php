<?php

namespace Database\Factories;

use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supervisor>
 */
class SupervisorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'designation' => 'Test',
            'host_establishment' => 'Test',
            'campus_type' => Supervisor::IN_CAMPUS,
            'working_day_start' => null,
            'working_day_end' => null,
            'portal_id' => 0,
            'coordinator_id' => 0,
        ];
    }
}
