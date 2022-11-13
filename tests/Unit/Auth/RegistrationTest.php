<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class RegistrationTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * Auth User registration test
     *
     * @return void
     */
    public function test_auth_user_can_register() :void
    {
        $response = $this->post('/api/v1/register', [
            'name' => 'testUser',
            'email' => 'me@me.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertStatus(201);

    }

}
