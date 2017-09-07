<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_change_password()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('Password1')
        ]);

        $user->changePassword('Password2');

        $user->fresh();
        $this->assertTrue(Hash::check('Password2', $user->password));
    }

    /** @test */
    function can_get_user_by_email()
    {
        factory(User::class)->create([
            'email' => 'jo@example.com'
        ]);

        $foundUser = User::findByEmail('jo@example.com');

        $this->assertNotNull($foundUser);
        $this->assertEquals('jo@example.com', $foundUser->email);
    }

    /** @test */
    function can_create_user_token()
    {
        $user = factory(User::class)->create([
            'email' => 'jo@example.com'
        ]);

        $user->createToken();

        $this->assertNotNull($user->token);
    }
}
