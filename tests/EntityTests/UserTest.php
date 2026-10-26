<?php

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use App\Entity\Meeting;
use App\Entity\Review;
use App\Entity\Attendee;
use App\Entity\FavoriteBook;

class UserTest extends TestCase {

    public function test_SettersAndGetters() {
        $user = new User();

        $user->setId('42');
        $this->assertEquals('42', $user->getId());

        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());

        $user->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());

        $user->setPassword("abc1234");
        $this->assertEquals("abc1234", $user->getPassword());

        $user->setUsername("Testperson");
        $this->assertEquals("Testperson", $user->getUsername());

        $user->setEmail("test@gmail.com");
        $this->assertEquals("test@gmail.com", $user->getEmail());
        $this->assertEquals("test@gmail.com", $user->getUserIdentifier());

        $user->setPicture("testpicture");
        $this->assertEquals("testpicture", $user->getPicture());

        $user->setBio("This is my bio.");
        $this->assertEquals("This is my bio.", $user->getBio());
    }

    public function test_MeetingsMethods() {
        $user = new User();
        $meeting1 = new Meeting();
        $meeting2 = new Meeting();

        $user->addMeeting($meeting1);
        $meetings = $user->getMeetings();

        $this->assertCount(1, $meetings);
        $this->assertTrue($meetings->contains($meeting1));
        $this->assertSame($user, $meeting1->getUser());

        $user->addMeeting($meeting2);
        $updatedMeetings = $user->getMeetings();

        $this->assertCount(2, $updatedMeetings);
        $this->assertTrue($updatedMeetings->contains($meeting2));

        $user->removeMeeting($meeting1);
        $finalMeetings = $user->getMeetings();

        $this->assertCount(1, $finalMeetings);
        $this->assertFalse($finalMeetings->contains($meeting1));
        $this->assertTrue($finalMeetings->contains($meeting2));
        $this->assertNull($meeting1->getUser());
    }

    public function test_ReviewMethods() {
        $user = new User();
        $review1 = new Review();
        $review2 = new Review();

        $user->addReview($review1);
        $reviews = $user->getReviews();

        $this->assertCount(1, $reviews);
        $this->assertTrue($reviews->contains($review1));
        $this->assertSame($user, $review1->getUser());

        $user->addReview($review2);
        $updatedReviews = $user->getReviews();

        $this->assertCount(2, $updatedReviews);
        $this->assertTrue($updatedReviews->contains($review2));

        $user->removeReview($review1);
        $finalReviews = $user->getReviews();

        $this->assertCount(1, $finalReviews);
        $this->assertFalse($finalReviews->contains($review1));
        $this->assertTrue($finalReviews->contains($review2));
        $this->assertNull($review1->getUser());
    }

    public function test_AttendeeMethods() {
        $user = new User();
        $attendee1 = new Attendee();
        $attendee2 = new Attendee();

        $user->addAttendee($attendee1);
        $attendees = $user->getAttendees();

        $this->assertCount(1, $attendees);
        $this->assertTrue($attendees->contains($attendee1));
        $this->assertSame($user, $attendee1->getUser());

        $user->addAttendee($attendee2);
        $updatedAttendees = $user->getAttendees();

        $this->assertCount(2, $updatedAttendees);
        $this->assertTrue($updatedAttendees->contains($attendee2));

        $user->removeAttendee($attendee1);
        $finalAttendees = $user->getAttendees();

        $this->assertCount(1, $finalAttendees);
        $this->assertFalse($finalAttendees->contains($attendee1));
        $this->assertTrue($finalAttendees->contains($attendee2));
        $this->assertNull($attendee1->getUser());
    }

    public function test_FavoriteBookMethods()
    {
        $user = new User();
        $fave1 = new FavoriteBook();
        $fave2 = new FavoriteBook();

        $user->addFavoriteBook($fave1);
        $faveBooks = $user->getFavoriteBooks();

        $this->assertCount(1, $faveBooks);
        $this->assertTrue($faveBooks->contains($fave1));
        $this->assertSame($user, $fave1->getUser());

        $user->addFavoriteBook($fave2);
        $updatedFaveBooks = $user->getFavoriteBooks();

        $this->assertCount(2, $updatedFaveBooks);
        $this->assertTrue($updatedFaveBooks->contains($fave2));

        $user->removeFavoriteBook($fave1);
        $finalFaveBooks = $user->getFavoriteBooks();

        $this->assertCount(1, $finalFaveBooks);
        $this->assertFalse($finalFaveBooks->contains($fave1));
        $this->assertTrue($finalFaveBooks->contains($fave2));
        $this->assertNull($fave1->getUser());
    }
}