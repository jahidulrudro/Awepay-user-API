<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LoginTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * Auth user login test
     *
     * @return void
     */
    public function test_auth_user_can_login() :void
    {
        $response = $this->post('/api/v1/login', [
            'email' => 'me@me.com',
            'password' => '123456',
        ])->assertOk();

        $this->assertArrayHasKey('token', $response->json());

    }

    /**
     * @return void
     */

    public function test_if_user_email_is_not_available_then_it_return_error(): void
    {
        $response = $this->post('/api/v1/login', [
            'email' => 'me@me.com',
            'password' => '123456',
        ])->assertUnauthorized();

    }

}
