<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LockThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function non_admin_cannot_lock_threads() {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('lock-threads.store', $thread))->assertStatus(403);

        $this->assertFalse(!!$thread->fresh()->locked);
    }

    /** @test */
    public function admin_users_can_lock_threads() {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread');

        $this->post(route('lock-threads.store', $thread))->assertStatus(204);

        $this->assertTrue(!!$thread->fresh()->locked);
    }

    /** @test */
    public function admin_users_can_unlock_threads() {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread', ['locked' => true]);

        $this->delete(route('lock-threads.destroy', $thread))->assertStatus(204);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function once_locked_thread_may_not_receive_new_replies() {
        $this->signIn();

        $thread = create('App\Thread', ['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Some text',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
