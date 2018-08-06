<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function a_user_has_a_profile() {
        $user = create("App\User");

        $this->get("/profile/{$user->name}")
            ->assertSee($user->name);
    }

    /** @test */
    public function profiles_contains_all_threads_created_by_associated_user() {
        $this->signIn();

        $thread = create("App\Thread", ["user_id" => auth()->id()]);

        $this->get("/profile/" . auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
