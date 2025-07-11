<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710054620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE missing_part_id_seq CASCADE');
        $this->addSql('ALTER TABLE set_part DROP is_missing');
        $this->addSql('ALTER INDEX idx_e13586b510fb0d18 RENAME TO IDX_DCC8924F10FB0D18');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE missing_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE set_part ADD is_missing BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER INDEX idx_dcc8924f10fb0d18 RENAME TO idx_e13586b510fb0d18');
    }
}
