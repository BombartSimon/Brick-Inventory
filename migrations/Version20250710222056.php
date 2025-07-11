<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710222056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add CASCADE DELETE constraints for Set->SetPart->MissingPart relationships';
    }

    public function up(Schema $schema): void
    {
        // Add CASCADE DELETE to set_part foreign key
        $this->addSql('ALTER TABLE set_part DROP CONSTRAINT fk_e13586b510fb0d18');
        $this->addSql('ALTER TABLE set_part ADD CONSTRAINT fk_e13586b510fb0d18 FOREIGN KEY (set_id) REFERENCES "set" (id) ON DELETE CASCADE');
        
        // Add CASCADE DELETE to missing_part foreign key
        $this->addSql('ALTER TABLE missing_part DROP CONSTRAINT fk_e13586b54ce34bec');
        $this->addSql('ALTER TABLE missing_part ADD CONSTRAINT fk_e13586b54ce34bec FOREIGN KEY (part_id) REFERENCES set_part (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Revert to RESTRICT (default behavior)
        $this->addSql('ALTER TABLE set_part DROP CONSTRAINT fk_e13586b510fb0d18');
        $this->addSql('ALTER TABLE set_part ADD CONSTRAINT fk_e13586b510fb0d18 FOREIGN KEY (set_id) REFERENCES "set" (id)');
        
        $this->addSql('ALTER TABLE missing_part DROP CONSTRAINT fk_e13586b54ce34bec');  
        $this->addSql('ALTER TABLE missing_part ADD CONSTRAINT fk_e13586b54ce34bec FOREIGN KEY (part_id) REFERENCES set_part (id)');
    }
}
