<?php

use PHPUnit\Framework\TestCase;
use App\Entity\FavoriteBook;

class FavoriteBookTest extends TestCase {

    public function test_getAndSetId() {
        $favoriteBook = new FavoriteBook();

        $favoriteBook->setId(42);
        $this->assertEquals(42, $favoriteBook->getId());
    }
}