<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationTest extends TestCase
{
    use DatabaseMigrations, WithFaker;


    public function setUp()
    {
        parent::setUp();
        $this->signIn();

    }

    /** @test */
    public function a_notification_is_prepared_when_a_new_reply_is_created_that_is_not_by_current_user()
    {

        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'body' => $this->faker->sentence(),
            'user_id' => auth()->id()
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'body' => $this->faker->sentence(),
            'user_id' => create('App\User')->id
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_notifications()
    {

        create(DatabaseNotification::class);

        $user = auth()->user();

        $this->assertCount(
            1,
            $this->getJson("/profile/{$user->name}/notifications")->json()
        );
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {

        create(DatabaseNotification::class);

        $user = auth()->user();

        tap(auth()->user(), function ($user) {
            $this->assertCount(1, $user->unreadNotifications);

            $this->delete("/profile/{$user->name}/notifications/". $user->unreadNotifications->first()->id);

            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }
}
