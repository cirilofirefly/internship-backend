<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'user_type'         => User::COORDINATOR,
            'middle_name'       => '',
            'birthday'          => now()->format('Y-d-m'),
            'nationality'       => 'N/A',
            'civil_status'      => 'N/A',
            'contact_number'    => 'N/A',
            'email'             => fake()->unique()->safeEmail(),
            'gender'            => 'male',
            'password'          => 'password',
            'suffix'            => '',
            'username'          => fake()->username(),
            'status'            => User::APPROVED,
            'profile_picture'   => '',
            'e_signature'       => ''
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
