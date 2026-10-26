<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Book;
use App\Entity\Review;
use App\Entity\FavoriteBook;
use App\Entity\Availability;

class BookTest extends TestCase {

    public function test_SettersAndGetters() {
        $book = new Book();

        $this->assertNull($book->getTitle());
        $book->setTitle("The Great Gatsby");
        $this->assertEquals("The Great Gatsby", $book->getTitle());

        $this->assertNull($book->getAuthor());
        $book->setAuthor("F. Scott Fitzgerald");
        $this->assertEquals("F. Scott Fitzgerald", $book->getAuthor());

        $this->assertNull($book->getAverageRating());
        $book->setAverageRating(3.93);
        $this->assertEquals(3.93, $book->getAverageRating());

        $this->assertNull($book->isTrending());
        $book->setTrending(1);
        $this->assertEquals(1, $book->isTrending());

        $this->assertNull($book->getCoverImage());
        $book->setCoverImage("test");
        $this->assertEquals("test", $book->getCoverImage());

        $this->assertNull($book->getId());
        $book->setId(42);
        $this->assertEquals(42, $book->getId());
    }

    public function test_ReviewMethods() {
        $book = new Book();
        $review1 = new Review();
        $review2 = new Review();

        $book->addReview($review1);
        $reviews = $book->getReviews();

        $this->assertCount(1, $reviews);
        $this->assertTrue($reviews->contains($review1));
        $this->assertSame($book, $review1->getBook());

        $book->addReview($review2);
        $updatedReviews = $book->getReviews();

        $this->assertCount(2, $updatedReviews);
        $this->assertTrue($updatedReviews->contains($review2));

        $book->removeReview($review1);
        $finalReviews = $book->getReviews();

        $this->assertCount(1, $finalReviews);
        $this->assertFalse($finalReviews->contains($review1));
        $this->assertTrue($finalReviews->contains($review2));
        $this->assertNull($review1->getBook());
    }

    public function test_FavoriteBookMethods()
    {
        $book = new Book();
        $fave1 = new FavoriteBook();
        $fave2 = new FavoriteBook();

        $book->addFavoriteBook($fave1);
        $faveBooks = $book->getFavoriteBooks();

        $this->assertCount(1, $faveBooks);
        $this->assertTrue($faveBooks->contains($fave1));
        $this->assertSame($book, $fave1->getBook());

        $book->addFavoriteBook($fave2);
        $updatedFaveBooks = $book->getFavoriteBooks();

        $this->assertCount(2, $updatedFaveBooks);
        $this->assertTrue($updatedFaveBooks->contains($fave2));

        $book->removeFavoriteBook($fave1);
        $finalFaveBooks = $book->getFavoriteBooks();

        $this->assertCount(1, $finalFaveBooks);
        $this->assertFalse($finalFaveBooks->contains($fave1));
        $this->assertTrue($finalFaveBooks->contains($fave2));
        $this->assertNull($fave1->getBook());
    }

    public function test_AvailabilityMethods()
    {
        $book = new Book();
        $availability1 = new Availability();
        $availability2 = new Availability();

        $book->addAvailability($availability1);
        $availabilities = $book->getAvailabilities();

        $this->assertCount(1, $availabilities);
        $this->assertTrue($availabilities->contains($availability1));
        $this->assertSame($book, $availability1->getBook());

        $book->addAvailability($availability2);
        $updatedAvailabilities = $book->getAvailabilities();

        $this->assertCount(2, $updatedAvailabilities);
        $this->assertTrue($updatedAvailabilities->contains($availability2));

        $book->removeAvailability($availability1);
        $finalAvailabilities = $book->getAvailabilities();

        $this->assertCount(1, $finalAvailabilities);
        $this->assertFalse($finalAvailabilities->contains($availability1));
        $this->assertTrue($finalAvailabilities->contains($availability2));
        $this->assertNull($availability1->getBook());
    }
}