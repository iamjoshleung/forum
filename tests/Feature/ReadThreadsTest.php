<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;
    
    public function setUp()
    {
        parent::setUp();

        $this->thread = create(\App\Thread::class);
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
            ->assertStatus(200)
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_view_a_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_threads_according_to_channel() {

        $channel = create(\App\Channel::class);
        $threadInChannel = create(\App\Thread::class, ['channel_id' => $channel->id]);
        $threadNotInChannel = create(\App\Thread::class);

        $this->get( "/threads/" . $channel->slug )
            ->assertStatus(200)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
          
    }

    /** @test */
    public function a_user_can_filter_threads_by_username() {
        $this->signIn( create('App\User', ['name' => 'JoshuaLeung']) );

        $threadByJL = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJL = create('App\Thread');

        $this->get('/threads?by=JoshuaLeung')
            ->assertSee($threadByJL->title)
            ->assertDontSee($threadNotByJL->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_unanwsered() {
        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id]);

        $res = $this->getJson('/threads?unanwsered=1')->json();

        $this->assertCount(1, $res['data']);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity() {
        // threads with 3, 2, 0 replies
        $threadWithThreeReplies = create('App\Thread', ['created_at' => new Carbon('-2 minute')]);
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithTwoReplies = create('App\Thread', ['created_at' => new Carbon('-1 minute')]);
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithNoReply = $this->thread;

        // get threads from route
        $response = $this->getJson('threads?popular=1')->json();
        // dd($response);
        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function thread_records_a_new_visit_each_time_the_it_is_read() {
        $thread = create('App\Thread');

        $this->assertSame(0, $thread->visits());

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits());
    }
}