<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627183434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_not_validated (id INT AUTO_INCREMENT NOT NULL, haircut_id INT NOT NULL, customer_id INT NOT NULL, title VARCHAR(255) NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, INDEX IDX_918AF6B01CFEFA25 (haircut_id), INDEX IDX_918AF6B09395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_not_validated ADD CONSTRAINT FK_918AF6B01CFEFA25 FOREIGN KEY (haircut_id) REFERENCES haircut (id)');
        $this->addSql('ALTER TABLE event_not_validated ADD CONSTRAINT FK_918AF6B09395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE event_not_validated');
    }
}
