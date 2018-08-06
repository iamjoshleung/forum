<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function a_reply_blongs_to_a_owner()
    {
        $reply = factory(\App\Reply::class)->create();

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    public function it_knows_if_a_reply_was_just_published_one_minute_ago() {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_detects_all_mentioned_users_in_the_body() {
        $reply = create('App\Reply', [
            'body' => '@JohnDoe wants to talk to @JaneDoe'
        ]);

        $this->assertEquals(['JohnDoe', 'JaneDoe'], $reply->mentionedUsers());
    }

    /** @test */
    public function it_wraps_mentioned_user_in_an_anchor_tag() {
        $reply = new \App\Reply([
            'body' => 'Hello @Jane-Doe.'
        ]);

        $this->assertEquals('Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.', $reply->body);
    }

    /** @test */
    public function it_knows_if_it_is_the_best_reply() {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }

    /** @test */
    public function a_reply_is_sanitized_automatically() {
        $reply = create('App\Reply', ['body' => '<script>alert("click me")</script><h1>hello</h1>']);

        $this->assertEquals('<h1>hello</h1>', $reply->body);
    }
}
