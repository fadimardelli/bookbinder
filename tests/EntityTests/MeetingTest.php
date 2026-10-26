<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Meeting;
use App\Entity\Attendee;
use App\Entity\Book;

class MeetingTest extends TestCase {

    public function test_SettersAndGetters() {
        $meeting = new Meeting();

        //getDate and setDate
        $this->assertNull($meeting->getDate());
        $date = new DateTime();
        $result = $meeting->setDate($date);
        $this->assertInstanceOf(Meeting::class, $result);
        $this->assertInstanceOf(DateTimeInterface::class, $meeting->getDate());
        $this->assertEquals($date, $meeting->getDate());

        //getDescription and setDescription
        $this->assertNull($meeting->getDescription());
        $meeting->setDescription("This is a description!");
        $this->assertEquals("This is a description!", $meeting->getDescription());

        //getBook and setBook
        $book = new Book();
        $this->assertNull($meeting->getBook());
        $meeting->setBook($book);
        $this->assertEquals($book, $meeting->getBook());

        //setId and getId
        $this->assertNull($meeting->getId());
        $meeting->setId(42);
        $this->assertEquals(42, $meeting->getId());
    }

    public function test_AttendeeMethods() {
        $meeting = new Meeting();
        $attendee1 = new Attendee();
        $attendee2 = new Attendee();

        $meeting->addAttendee($attendee1);
        $attendees = $meeting->getAttendees();

        $this->assertCount(1, $attendees);
        $this->assertTrue($attendees->contains($attendee1));
        $this->assertSame($meeting, $attendee1->getMeeting());

        $meeting->addAttendee($attendee2);
        $updatedAttendees = $meeting->getAttendees();

        $this->assertCount(2, $updatedAttendees);
        $this->assertTrue($updatedAttendees->contains($attendee2));

        $meeting->removeAttendee($attendee1);
        $finalAttendees = $meeting->getAttendees();

        $this->assertCount(1, $finalAttendees);
        $this->assertFalse($finalAttendees->contains($attendee1));
        $this->assertTrue($finalAttendees->contains($attendee2));
        $this->assertNull($attendee1->getMeeting());
    }
}