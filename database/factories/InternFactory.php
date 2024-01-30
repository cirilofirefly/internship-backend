<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intern>
 */
class InternFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'student_number' => '1800441',
            'coordinator_id' => 0,
            'portal_id'      => 0,
            'is_assigned'    => 1,
            'college'        => 'test',
            'program'        => '',
            'section'        => '',
            'year_level'     => ''
        ];
    }
}
