<?php

namespace Tests\Feature;

use App\Trending;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected $trending;

    protected function setUp() {
        parent::setUp();

        $this->trending = new Trending();

        $this->trending->reset();
    }

    // protected function tearDown()
    // {
    //     parent::tearDown();

    //     $this->trending->reset();
    // }
    
    /** @test */
    public function it_increments_a_threads_score_each_time_it_is_read() {
        $this->assertEmpty($this->trending->get());

        $thread = create('App\Thread');

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending = $this->trending->get());

        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
