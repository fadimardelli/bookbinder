<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230517101421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendee (id INT AUTO_INCREMENT NOT NULL, meeting_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1150D56767433D9C (meeting_id), INDEX IDX_1150D567A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, library_id INT NOT NULL, book_id INT NOT NULL, total_count INT NOT NULL, current_count INT NOT NULL, INDEX IDX_3FB7A2BFFE2541D7 (library_id), INDEX IDX_3FB7A2BF16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL, author VARCHAR(60) NOT NULL, average_rating DOUBLE PRECISION NOT NULL, trending TINYINT(1) NOT NULL, cover_image LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite_book (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_25E3BA0EA76ED395 (user_id), INDEX IDX_25E3BA0E16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE library (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, location VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_F515E13964D218E (location_id), INDEX IDX_F515E139A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, review LONGTEXT DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C616A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D56767433D9C FOREIGN KEY (meeting_id) REFERENCES meeting (id)');
        $this->addSql('ALTER TABLE attendee ADD CONSTRAINT FK_1150D567A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFFE2541D7 FOREIGN KEY (library_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE favorite_book ADD CONSTRAINT FK_25E3BA0EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favorite_book ADD CONSTRAINT FK_25E3BA0E16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E13964D218E FOREIGN KEY (location_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D56767433D9C');
        $this->addSql('ALTER TABLE attendee DROP FOREIGN KEY FK_1150D567A76ED395');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFFE2541D7');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BF16A2B381');
        $this->addSql('ALTER TABLE favorite_book DROP FOREIGN KEY FK_25E3BA0EA76ED395');
        $this->addSql('ALTER TABLE favorite_book DROP FOREIGN KEY FK_25E3BA0E16A2B381');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E13964D218E');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C616A2B381');
        $this->addSql('DROP TABLE attendee');
        $this->addSql('DROP TABLE availability');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE favorite_book');
        $this->addSql('DROP TABLE library');
        $this->addSql('DROP TABLE meeting');
        $this->addSql('DROP TABLE review');
    }
}
