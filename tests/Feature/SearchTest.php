<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads() {

        config(['scout.driver' => 'algolia']);
        
        $search = 'foobar';

        create('App\Thread', [], 2);
        create('App\Thread', ['body' => "This thread contains {$search}"], 2);

        do{
            sleep(.25);
            $results = $this->getJson("/threads/search?q={$search}")->json()['data'];
        } while( empty($results) );

        // dd($res);

        $this->assertCount(2, $results);

        Thread::latest()->take(4)->unsearchable();
    }
}
