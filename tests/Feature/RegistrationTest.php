<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'Joshua',
            'email' => 'joshua@example.com',
            'password' => 'fooooo',
            'password_confirmation' => 'fooooo'
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    /** @test */
    public function user_can_fully_confirm_their_email_addresses()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'Joshua',
            'email' => 'joshua@example.com',
            'password' => 'fooooo',
            'password_confirmation' => 'fooooo'
        ]);

        $user = User::whereName('Joshua')->first();

        $this->assertFalse($user->confirmed);

        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect('/threads')
            ->assertSessionHas('flash');

        tap($user->fresh(), function($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    /** @test */
    public function confirming_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect('/threads')
            ->assertSessionHas('flash', 'Invalid token.');
    }
}
