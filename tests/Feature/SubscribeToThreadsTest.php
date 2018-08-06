<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use WithFaker, DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads() {
        $this->signIn();

        $thread = create('App\Thread');

        $this->post("{$thread->path()}/subscriptions");

        $this->assertCount(1, $thread->subscriptions);

    }

    /** @test */
    public function a_user_can_unsubscribe_to_threads() {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->subscribe();

        $this->delete("{$thread->path()}/subscriptions");

        $this->assertCount(0, $thread->subscriptions);
    }
}
