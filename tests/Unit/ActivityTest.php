<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function it_records_activity_when_a_thread_is_created() {
        $this->signIn();

        $thread = create('App\Thread');

        $this->assertDatabaseHas('activities', [
            'user_id' => auth()->id(),
            'type' => 'created_thread',
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);

        $activity = \App\Activity::first();

        $this->assertEquals($thread->id, $activity->subject->id);
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created() {
        $this->signIn();

        $reply = create('App\Reply');

        $this->assertEquals(2, \App\Activity::count());
    }

    /** @test */
    public function it_fetches_a_feed_for_any_user() {
        // given a thread, and another thread from a week ago
        $this->signIn();
        create('App\Thread', ['user_id' => auth()->id()], 2);

        auth()->user()->activity()->first()->update([
            'created_at' => Carbon::now()->subWeek()
        ]);
        // when we fetch the feed
        $feed = \App\Activity::feed(auth()->user());
        // then it should return a proper format
        $this->assertTrue($feed->keys()->contains(Carbon::now()->format('Y-m-d')));
        $this->assertTrue($feed->keys()->contains(Carbon::now()->subWeek()->format('Y-m-d')));
    }
}
