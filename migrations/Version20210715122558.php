<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210715122558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE date ADD member_id INT NOT NULL');
        $this->addSql('ALTER TABLE date ADD CONSTRAINT FK_AA9E377A7597D3FE FOREIGN KEY (member_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_AA9E377A7597D3FE ON date (member_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06 ON user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE date DROP FOREIGN KEY FK_AA9E377A7597D3FE');
        $this->addSql('DROP INDEX IDX_AA9E377A7597D3FE ON date');
        $this->addSql('ALTER TABLE date DROP member_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON `user` (name)');
    }
}
