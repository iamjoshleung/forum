<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BestReplyTest extends TestCase
{

    use DatabaseMigrations;
    
    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply() {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->postJson(route('best-reply.store', $replies[1]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function only_the_thread_creator_may_mark_a_reply_as_best() {
        $this->withExceptionHandling();
        
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn(create('App\User'));

        $this->postJson(route('best-reply.store', $replies[1]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function when_a_best_reply_is_deleted_its_associated_thread_will_reflect_that_change() {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('reply.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
