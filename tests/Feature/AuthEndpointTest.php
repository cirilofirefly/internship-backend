<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{Coordinator, Intern, Supervisor, User};

class AuthEndpointTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_not_found_response()
    {
        $response = $this->get('/');

        $response->assertStatus(404);
    }

    public function test_the_user_cannot_login()
    {

        $user = User::factory()->create(['user_type' => User::COORDINATOR]);
        Coordinator::factory()->create(['portal_id' => $user->id, 'program' => 'bsit']);

        $response = $this->post('/api/login', [
            'username' => $user->username,
            'password' => 'password123'
        ]);

        $response->assertStatus(401);
    }

    public function test_the_coordinator_can_login()
    {

        $user = User::factory()->create(['user_type' => User::COORDINATOR]);
        Coordinator::factory()->create(['portal_id' => $user->id, 'program' => 'bsit']);

        $response = $this->post('/api/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_the_supervisor_can_login()
    {

        $user = User::factory()->create(['user_type' => User::COORDINATOR]);
        $coordinator = Coordinator::factory()->create(['portal_id' => $user->id, 'program' => 'bsit']);

        $user = User::factory()->create(['user_type' => User::SUPERVISOR]);

        Supervisor::factory()->create([
            'portal_id'         => $user->id,
            'coordinator_id'    => $coordinator->id,
            'designation'       => 'CAS',
            'host_establishment'=> 'LNU',
            'campus_type'       => Supervisor::IN_CAMPUS,
        ]);

        $response = $this->post('/api/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_the_intern_can_login()
    {
        $student_number = date('y') . mt_rand(10000, 99999);
        $user = User::factory()->create(['user_type' => User::COORDINATOR]);
        $coordinator = Coordinator::factory()->create(['portal_id' => $user->id, 'program' => 'bsit']);
        $user = User::factory()->create(['user_type' => User::INTERN, 'username' => $student_number]);

        Intern::factory()->create([
            'student_number'        => $student_number,
            'year_level'            => '4th Year',
            'college'               => 'College of Arts and Sciences',
            'program'               => 'bsit',
            'section'               => 'AI-41',
            'portal_id'             => $user->id,
            'coordinator_id'    => $coordinator->id
        ]);


        $response = $this->post('/api/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_the_intern_can_register()
    {

        $user = User::factory()->create(['user_type' => User::COORDINATOR]);
        $coordinator = Coordinator::factory()->create(['portal_id' => $user->id, 'program' => 'bsit']);
        $student_number = date('y') . mt_rand(10000, 99999);
        $user = User::factory()->make(['user_type' => User::INTERN]);

        $data = [
            'username'          => $student_number,
            'password'          => 'password',
            'password_confirmation' => 'password',
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'middle_name'       => $user->middle_name,
            'suffix'            => $user->suffix,
            'gender'            => 'male',
            'birthday'          => now()->format('Y-m-d'),
            'email'             => $user->email,
            'nationality'       => $user->nationality,
            'civil_status'      => $user->civil_status,
            'contact_number'    => $user->contact_number,
            'student_number'    => $student_number,
            'year_level'        => '4th Year',
            'college'           => 'College of Arts and Sciences',
            'program'           => 'bsit',
            'section'           => 'AI-41',
            'coordinator_id'    => $coordinator->id
        ];


        $response = $this->post('/api/register-intern', $data);
        $response->assertStatus(200);
    }

    public function test_the_intern_invalid_registration()
    {

        $user = User::factory()->create(['user_type' => User::COORDINATOR]);
        $coordinator = Coordinator::factory()->create(['portal_id' => $user->id, 'program' => 'bsit']);
        $student_number = date('y') . mt_rand(10000, 99999);
        $user = User::factory()->make(['user_type' => User::INTERN]);

        $data = [
            'username'          => $student_number,
            'password'          => 'password',
            'password_confirmation' => 'password123',
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'middle_name'       => $user->middle_name,
            'suffix'            => $user->suffix,
            'gender'            => 'male',
            'birthday'          => now()->format('Y-m-d'),
            'email'             => $user->email,
            'nationality'       => $user->nationality,
            'civil_status'      => $user->civil_status,
            'contact_number'    => $user->contact_number,
            'student_number'    => $student_number,
            'year_level'        => '4th Year',
            'college'           => 'College of Arts and Sciences',
            'program'           => 'bsit',
            'section'           => 'AI-41',
            'coordinator_id'    => $coordinator->id
        ];

        $response = $this->post('/api/register-intern', $data);
        $response->assertStatus(302);
    }
}
