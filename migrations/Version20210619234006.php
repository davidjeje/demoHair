<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210619234006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
 
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE date (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, endT DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, haircut_id INT DEFAULT NULL, member_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, start DATETIME NOT NULL, endT DATETIME NOT NULL, INDEX IDX_3BAE0AA71CFEFA25 (haircut_id), INDEX IDX_3BAE0AA77597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE haircut (id INT AUTO_INCREMENT NOT NULL, paginator_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_FC3831C736790833 (paginator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paginator (id INT AUTO_INCREMENT NOT NULL, page INT NOT NULL, nb_pages INT NOT NULL, nom_route VARCHAR(255) NOT NULL, params_route LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', email VARCHAR(255) NOT NULL, number INT NOT NULL, token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA71CFEFA25 FOREIGN KEY (haircut_id) REFERENCES haircut (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA77597D3FE FOREIGN KEY (member_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE haircut ADD CONSTRAINT FK_FC3831C736790833 FOREIGN KEY (paginator_id) REFERENCES paginator (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA71CFEFA25');
        $this->addSql('ALTER TABLE haircut DROP FOREIGN KEY FK_FC3831C736790833');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA77597D3FE');
        $this->addSql('DROP TABLE date');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE haircut');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE paginator');
        $this->addSql('DROP TABLE `user`');
    }
}
