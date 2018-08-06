<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_mentioned_user_in_a_reply_is_notified() {
        // given a signed in user JohnDoe
        // when he mentions another user JaneDoe in a reply
        // JaneDoe will be notified
        
        $john = create('App\User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        $jane = create('App\User', ['name' => 'JaneDoe']);

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => '@JaneDoe is mentioned here',
            'user_id' => $john->id
        ]);

        $this->postJson($thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function it_fetches_all_mentioned_users_starting_with_the_given_characters() {
        create('App\User', ['name' => 'johndoe' ]);
        create('App\User', ['name' => 'johndoe2' ]);
        create('App\User', ['name' => 'janedoe' ]);

        $this->json('GET', 'api/users', ['q' => 'john'])
            ->assertJsonCount(2);
    }
}
