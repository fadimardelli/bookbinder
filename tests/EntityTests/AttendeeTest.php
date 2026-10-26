<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Attendee;

class AttendeeTest extends TestCase {

    public function test_getAndSetId() {
        $attendee = new Attendee();

        $attendee->setId(42);
        $this->assertEquals(42, $attendee->getId());
    }
}