<?php

namespace Tests\Unit;

use Tests\TestCase;
// use function foo\func;
use Illuminate\Support\Facades\Redis;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp() {
        parent::setUp();

        $this->thread = factory(\App\Thread::class)->create();
    }

    /** @test */
    public function a_thread_has_replies()
    {        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_has_a_creator() {

        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /** @test */
    public function it_can_add_replies() {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function it_notifies_all_users_who_subscribed() {
        Notification::fake();

        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'body' => 'Foobar',
                'user_id' => 999
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function it_belongs_to_a_channel() {
        $this->assertInstanceOf('App\Channel', $this->thread->channel);
    }

    /** @test */
    public function a_thread_has_a_path() {
        $thread = create('App\Thread');

        $this->assertEquals(
            "/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path()
        );
    }

    /** @test */
    public function a_thread_can_be_subscribed_to() {
        $thread = create('App\Thread');

        $this->signIn();

        $thread->subscribe();

        $this->assertEquals(
            1, 
            $thread->subscriptions()->where('user_id', auth()->id())->count());
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from() {
        $thread = create('App\Thread');

        $thread->subscribe($userId = 1);
        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions);
    }

    /** @test */
    public function it_knows_if_the_authenticated_user_subscribed_to_it() {
        $thread = create('App\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    public function it_can_check_if_the_authenticated_user_has_read_the_thread() {
        $this->signIn();

        $thread = create('App\Thread');

        tap(auth()->user(), function($user) use ($thread) {
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->readThread($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }

    /** @test */
    public function a_thread_is_sanitized_automatically() {
        $thread = create('App\Thread', ['body' => '<script>alert("click me")</script><h1>hello</h1>']);

        $this->assertEquals('<h1>hello</h1>', $thread->body);
    }
}
