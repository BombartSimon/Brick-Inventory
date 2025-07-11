<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710205948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE missing_part (id SERIAL NOT NULL, part_id INT DEFAULT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E13586B54CE34BEC ON missing_part (part_id)');
        $this->addSql('ALTER TABLE missing_part ADD CONSTRAINT FK_E13586B54CE34BEC FOREIGN KEY (part_id) REFERENCES set_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE missing_part DROP CONSTRAINT FK_E13586B54CE34BEC');
        $this->addSql('DROP TABLE missing_part');
    }
}
