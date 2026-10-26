<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Availability;

class AvailabilityTest extends TestCase {

    public function test_SettersAndGetters() {
        $availability = new Availability();

        $availability->setTotalCount(5);
        $this->assertEquals(5, $availability->getTotalCount());

        $availability->setCurrentCount(4);
        $this->assertEquals(4, $availability->getCurrentCount());

        $availability->setId(42);
        $this->assertEquals(42, $availability->getId());
    }
}