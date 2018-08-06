<?php

namespace Tests\Feature;

use Mockery;
use App\Thread;
use App\Activity;
use Tests\TestCase;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 
     * 
     * @return 
     */
    public function setUp() {
        parent::setUp();

        app()->singleton(Recaptcha::class, function() {
            return Mockery::mock(Recaptcha::class, function($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }
    
    /** @test */
    public function a_user_can_create_threads() {
        $this->signIn();

        $thread = make(\App\Thread::class);

        $response = $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test']);

        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    public function guests_cannot_create_threads() {
        $this->withExceptionHandling();
        
        $this->get('/threads/create')
            ->assertRedirect(route('login'));

        $this->post(route('threads'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function a_thread_requires_a_title() {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body() {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_recaptcha_verification() {
        unset(app()[Recaptcha::class]);
        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_thread_requires_a_unique_slug() {
        $this->signIn();

        create('App\Thread', [], 2);

        $thread = create('App\Thread', ['title' => 'Foo Bar']);

        $this->assertEquals('foo-bar', $thread->fresh()->slug); 

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test'])->json();

        $this->assertEquals("foo-bar-{$thread['id']}", $thread['slug']);

    }

    /** @test */
    public function a_thread_with_a_slug_that_ends_with_a_number_should_generate_a_proper_slug() {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Foo Bar 2']);

        $this->assertEquals('foo-bar-2', $thread->fresh()->slug); 

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test'])->json();

        $this->assertEquals("foo-bar-2-{$thread['id']}", $thread['slug']);

    }

    /** @test */
    public function a_thread_requires_a_valid_channel_id() {
        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_threads() {
        $this->withExceptionHandling();
        $thread = create('App\Thread');
        $this->delete($thread->path())->assertRedirect('/login');
        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test */
    public function new_users_must_first_confirm_their_email_address_before_creating_threads() {
        Mail::fake();
        $user = factory('App\User')->states('unconfirmed')->create();
        $this->signIn($user);
        $thread = make('App\Thread');
        $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You must first confirm your email before creating threads');
    }

    /** @test */
    public function authorized_users_can_delete_threads() {
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);
        $response = $this->json('DELETE', $thread->path());
        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, Activity::count());
    }

    public function publishThread($override = []) {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $override);

        return $this->post(route('threads'), $thread->toArray());
    }
}
