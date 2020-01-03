<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Mail\UserConfirmationEmail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Auth\UserConfirmationController
 */
class UserConfirmationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_user_gets_a_confirmation_email()
    {
        Mail::fake();

        $response = $this->withoutExceptionHandling()
            ->json('post', route('register'), [
                'name' => 'Phoenix Johnson',
                'email' => 'phoenix@example.com',
                'password' => 'Password1',
                'password_confirmation' => 'Password1',
            ]);

        $response->assertStatus(200);

        Mail::assertSent(UserConfirmationEmail::class, function ($mail) {
            return $mail->hasTo('phoenix@example.com')
                && $mail->user->id === User::findByEmail('phoenix@example.com')->id;
        });
    }

    /** @test */
    public function clicking_on_confirmation_link_confirms_user()
    {
        $user = factory(User::class)->create([
            'email' => 'jo@example.com',
        ]);
        $user->createToken('email');

        $response = $this->withoutExceptionHandling()
            ->get("/register/verify/{$user->emailToken->token}?email=jo@example.com");

        $user->refresh();
        $response->assertRedirect('/home');

        $this->assertNotNull($user->fresh()->confirmed_at);
        $this->assertNull($user->emailToken);
    }

    /** @test */
    public function can_resend_email()
    {
        Mail::fake();

        $user = factory(User::class)->create([
            'email' => 'jo@example.com',
        ]);
        $user->createToken('email');

        $response = $this->withoutExceptionHandling()->actingAs($user)->get('/register/email/');

        $response->assertStatus(302);

        Mail::assertSent(UserConfirmationEmail::class, function ($mail) {
            return $mail->hasTo('jo@example.com')
                && $mail->user->id === User::findByEmail('jo@example.com')->id;
        });
    }
}