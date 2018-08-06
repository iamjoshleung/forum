<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    public function it_checks_for_invalid_keywords() {
        $spam = new Spam;

        $this->assertFalse( $spam->detect('innocent reply') );

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }

    /** @test */
    public function it_checks_for_key_hold_down() {
        $spam = new Spam;

        $this->expectException(\Exception::class);

        $spam->detect('aaaaaaa');
    }


}