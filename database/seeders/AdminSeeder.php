<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name'        => 'Internship',
            'last_name'         => 'Super Admin',
            'user_type'         => User::SUPER_ADMIN,
            'middle_name'       => '',
            'birthday'          => '1999-29-09',
            'nationality'       => 'N/A',
            'civil_status'      => 'N/A',
            'contact_number'    => 'N/A',
            'email'             => 'super.admin@internship.com',
            'gender'            => 'male',
            'password'          => 'Test12345',
            'suffix'            => '',
            'username'          => 'superadmin',
            'status'            => User::APPROVED,
            'profile_picture'   => '',
            'e_signature'       => ''
        ]);

        User::create([
            'first_name'        => 'Internship',
            'last_name'         => 'Admin',
            'user_type'         => User::ADMIN,
            'middle_name'       => '',
            'birthday'          => '1999-29-09',
            'nationality'       => 'N/A',
            'civil_status'      => 'N/A',
            'contact_number'    => 'N/A',
            'email'             => 'admin@internship.com',
            'gender'            => 'male',
            'password'          => 'Test12345',
            'suffix'            => '',
            'username'          => 'admin',
            'profile_picture'   => '',
            'e_signature'       => ''
        ]);
    }
}
