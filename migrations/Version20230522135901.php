<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522135901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting ADD book_id INT NOT NULL');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E13916A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_F515E13916A2B381 ON meeting (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E13916A2B381');
        $this->addSql('DROP INDEX IDX_F515E13916A2B381 ON meeting');
        $this->addSql('ALTER TABLE meeting DROP book_id');
    }
}
