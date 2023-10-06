<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Coordinator;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoordinatorSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name'        => 'Mark Lester',
            'last_name'         => 'Laurente',
            'user_type'         => User::COORDINATOR,
            'middle_name'       => '',
            'birthday'          => '1999-29-09',
            'nationality'       => 'N/A',
            'civil_status'      => 'N/A',
            'contact_number'    => 'N/A',
            'email'             => 'marklester.laurente@lnu.edu.ph',
            'gender'            => 'male',
            'password'          => 'Test12345',
            'suffix'            => '',
            'username'          => 'marklesterlaurente',
            'status'            => User::APPROVED,
            'profile_picture'   => '',
            'e_signature'       => ''
        ]);

        Coordinator::create(['portal_id' => $user->id, 'program' => 'bsit']);
    }
}
