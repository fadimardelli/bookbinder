<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Library;
use App\Entity\Meeting;
use App\Entity\Availability;

class LibraryTest extends TestCase {

    public function test_GettersAndSetters() {
        $library = new Library();

        $library->setName("City Library of Antwerp");
        $this->assertEquals("City Library of Antwerp", $library->getName());

        $library->setLocation("Antwerp, Belgium");
        $this->assertEquals("Antwerp, Belgium", $library->getLocation());

        $library->setId(42);
        $this->assertEquals(42, $library->getId());
    }

    public function test_MeetingsMethods() {
        $library = new Library();
        $meeting1 = new Meeting();
        $meeting2 = new Meeting();

        $library->addMeeting($meeting1);
        $meetings = $library->getMeetings();

        $this->assertCount(1, $meetings);
        $this->assertTrue($meetings->contains($meeting1));
        $this->assertSame($library, $meeting1->getLocation());

        $library->addMeeting($meeting2);
        $updatedMeetings = $library->getMeetings();

        $this->assertCount(2, $updatedMeetings);
        $this->assertTrue($updatedMeetings->contains($meeting2));

        $library->removeMeeting($meeting1);
        $finalMeetings = $library->getMeetings();

        $this->assertCount(1, $finalMeetings);
        $this->assertFalse($finalMeetings->contains($meeting1));
        $this->assertTrue($finalMeetings->contains($meeting2));
        $this->assertNull($meeting1->getLocation());
    }

    public function test_AvailabilityMethods()
    {
        $library = new Library();
        $availability1 = new Availability();
        $availability2 = new Availability();

        $library->addAvailability($availability1);
        $availabilities = $library->getAvailabilities();

        $this->assertCount(1, $availabilities);
        $this->assertTrue($availabilities->contains($availability1));
        $this->assertSame($library, $availability1->getLibrary());

        $library->addAvailability($availability2);
        $updatedAvailabilities = $library->getAvailabilities();

        $this->assertCount(2, $updatedAvailabilities);
        $this->assertTrue($updatedAvailabilities->contains($availability2));

        $library->removeAvailability($availability1);
        $finalAvailabilities = $library->getAvailabilities();

        $this->assertCount(1, $finalAvailabilities);
        $this->assertFalse($finalAvailabilities->contains($availability1));
        $this->assertTrue($finalAvailabilities->contains($availability2));
        $this->assertNull($availability1->getLibrary());
    }

}