<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Review;

class ReviewTest extends TestCase {

    public function test_gettersAndSetters() {
        $review = new Review();

        $this->assertNull($review->getReview());
        $review->setReview("This is my opinion.");
        $this->assertEquals("This is my opinion.", $review->getReview());

        $this->assertNull($review->getRating());
        $review->setRating(3.75);
        $this->assertEquals(3.75, $review->getRating());

        $review->setId(42);
        $this->assertEquals(42, $review->getId());
    }
}