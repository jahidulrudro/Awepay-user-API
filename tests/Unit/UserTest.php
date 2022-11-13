<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    private const TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMWViYzNiNzBhNzczNTRiNzA1ZWQ4YjZlNGYzNjk3N2I2MDk1NzMyZTk2ZjYzMjU1ZGNjODk4MThjOTFjN2VmMmYwNTJjNDQ3NThhNWQ0NzIiLCJpYXQiOjE2NjgxNTEwNDguMDE1NDQ4LCJuYmYiOjE2NjgxNTEwNDguMDE1NDUyLCJleHAiOjE2OTk2ODcwNDguMDA1NjY4LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.d7tpYJHQtT0o98FlV0Qtdgh8Yp3-jFNk6L9fmhcVxsI12KBBia9-_OpyDb-88euDpvX_BXwhGlTv8B5MPAEHVaONxdYUpZoV2ynyCOfvlUsDxeU2QFMEbVjhBJJHFzky7hpk-tdmG6srZ3cJPVBog8oCi-mjD_0FfVa-juJSeahLkkqDnEsZujPojcAbaBSCCwzEdSCl0fpLqVkE600cOP7ZgktyzGn6DlfLKarDvYrvU4AmrDQHVV01IoSkUvPCrf24KzjhRl7rJO5cIxDESQ9duqam0t5ZbdUXCnQyiRC3EP6GyhT3B9fbYgKjos1Hs-8QJ5uktupLRYbzPo-3orI8XzrzFXNBaJYx1Hg5YS9JNhY1Qt396XxFVOEFWpwEvNXTIrRjpPrcRJKrAQT6KQDC9PaNkfEl6yHDEs27xeRibO_So9aunfRn5OF8w_65RfTj_JclOze56CFwaowqk_FYR96tWJ-6QDW0r-pnj2lLKZdxPI4aVhja_WrYQbJozBkdUF6TC95rJCMFx9_2LkIGTeqWaOUWnkis0dpvP7oG4O8XoOE4gq1BJUVKm9ABQ7cjMkqh1C26Hc_hItdwXdBgTXVQvFWLnT18nLp2CcWGqmZK5-FVUWgLHTeOWCY6ebg9pfxF1-gRGZz0ac8dDF9EsEk_Sc8Mc_TmYofw7mw';

    /**
     * POST: /api/v1/users
     *
     * @return void
     */
    public function test_should_create_user(): void
    {
        $data = [
            'fullName' => 'User name test',
            'email' => 'testdata@test.com',
            'phone' => '0172518616',
            'age' => '30',
        ];

        $response = $this->withHeader([
            'Authorization' => 'Bearer '. self::TOKEN,
            'Accept' => 'application/json',
        ])->post('api/v1/users', $data, []);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'fullName', 'email', 'phone', 'age',
            ],
            'message',
        ]);

        $response->seeJson([
            'success' => true,
        ]);
    }

    /**
     * PUT: /api/v1/user/:id
     * @return void
     */

     public function test_should_update_user(): void
     {
        $data = [
            'fullName' => 'User name test',
            'email' => 'testdata@test.com',
            'phone' => '0172518617',
            'age' => '60',
        ];

        $response = $this->withHeader([
            'Authorization' => 'Bearer '. self::TOKEN,
            'Accept' => 'application/json',
        ])->post('api/v1/users/1', $data, []);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'fullName', 'email', 'phone', 'age',
            ],
            'message',
        ]);

        $response->seeJson([
            'success' => true,
        ]);

     }

     /**
      * DELETE /api/v1/users/:id
      * @return void
      */

      public function test_should_delete_user(): void
      {
        $response = $this->withHeader([
            'Authorization' => 'Bearer '. self::TOKEN,
            'Accept' => 'application/json',
        ])->post('api/v1/users/1', []);

        $response->assertStatus(410);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'fullName', 'email', 'phone', 'age',
            ],
            'message',
        ]);

        $response->seeJson([
            'success' => true,
        ]);

      }

      /**
       * POST: /api/v1/users/search
       *  @return void
       */

     public function test_should_search_user(): void
     {
        $data = [
            'fullName' => 'User name test',
            'email' => 'testdata@test.com',
            'phone' => '0172518617',
            'order_by' => 'email',
            'order' => 'asc',
        ];

        $response = $this->withHeader([
            'Authorization' => 'Bearer '. self::TOKEN,
            'Accept' => 'application/json',
        ])->post('api/v1/users/search', $data, []);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'fullName', 'email', 'phone', 'age',
            ],
            'message',
        ]);

        $response->seeJson([
            'success' => true,
        ]);


     }
}
