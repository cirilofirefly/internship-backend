<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Intern;
use App\Models\User;
use Illuminate\Database\Seeder;

class InternSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name'        => 'Venus Amor',
            'last_name'         => 'Sablay',
            'user_type'         => User::INTERN,
            'middle_name'       => '',
            'birthday'          => '1999-29-09',
            'nationality'       => 'N/A',
            'civil_status'      => 'N/A',
            'contact_number'    => 'N/A',
            'email'             => '1800600@lnu.edu.ph',
            'gender'            => 'male',
            'password'          => 'Test12345',
            'suffix'            => '',
            'username'          => '1800600',
            'profile_picture'   => '',
            'e_signature'       => ''
        ]);

        Intern::create([
            'student_number'    => '1800600',
            'year_level'        => '4th Year',
            'college'           => 'College of Arts and Sciences',
            'program'           => 'Bachelor of Science in Information Technology',
            'section'           => 'AI-41',
            'portal_id'         => $user->id,
            'coordinator_id'    => 1
        ]);
    }
}
