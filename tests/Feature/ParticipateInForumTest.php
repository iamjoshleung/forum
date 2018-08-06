<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_unauthenticated_user_cant_post_replies_to_a_thread()
    {
        $thread = create(\App\Thread::class);

        $url = "{$thread->path()}/replies";
        $this->withExceptionHandling()
            ->post($url, [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_participate_in_a_thread()
    {
        $user = factory(\App\User::class)->create();
        $this->signIn();

        $thread = create(\App\Thread::class);
        $reply = make(\App\Reply::class);

        $url = "{$thread->path()}/replies";

        $this->post($url, $reply->toArray());

        $this->assertDatabaseHas('replies', [
            'body' => $reply->body
        ]);

        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create(\App\Thread::class);
        $reply = make(\App\Reply::class, ['body' => null]);

        $url = "{$thread->path()}/replies";

        $this->postJson($url, $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function unauthorized_users_cannot_delete_others_replies()
    {
        $this->withExceptionHandling();
        $reply = create('App\Reply');
        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('/login');

        $this->signIn();
        $this->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_their_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $this->delete("/replies/{$reply->id}")->assertStatus(302);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthroized_users_cannot_delete_others_replies()
    {
        $this->withExceptionHandling();
        $reply = create('App\Reply');
        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('/login');

        $this->signIn();
        $this->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_their_own_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $updatedReply = 'Updated';
        $this->patch("/replies/{$reply->id}", [
            'body' => $updatedReply
        ]);
        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply
        ]);
    }

    /** @test */
    public function a_user_can_fetch_all_replies_from_a_thread()
    {
        $thread = create('App\Thread');
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $response = $this->getJson($thread->path() . '/replies')->json();
        // dd($response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);

        $this->postJson($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function a_user_can_only_submit_a_reply_once_per_minute()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'Very simple reply'
        ]);

        $this->postJson($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->postJson($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
