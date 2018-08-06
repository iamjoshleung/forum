<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavouriteTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cannot_favourite_replies() {
        $this->withExceptionHandling()
            ->post('/replies/1/favourites')
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_favourite_any_replies() {
        $this->signIn();

        $reply = create('App\Reply');
        
        // hit endpoint for favouriting replies
        $this->post("/replies/{$reply->id}/favourites");

        // assert that the reply has been favourited
        $this->assertCount(1, $reply->favourites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavourite_a_reply() {
        $this->signIn();

        $reply = create('App\Reply');
        
        // hit endpoint for favouriting replies
        $reply->favourite();
        $this->delete("/replies/{$reply->id}/favourites");

        // assert that the reply has been favourited
        $this->assertCount(0, $reply->fresh()->favourites);
    }

    /** @test */
    public function an_authenticated_user_can_only_favourite_a_reply_once() {
        $this->signIn();

        $reply = create('App\Reply');
        
        try {
            $this->post("/replies/{$reply->id}/favourites");
            $this->post("/replies/{$reply->id}/favourites");
        } catch (\Exception $e) {
            $this->fail('An authenticated user cannot favourite a reply twice'); 
        }

        $this->assertCount(1, $reply->favourites);
    }
}
