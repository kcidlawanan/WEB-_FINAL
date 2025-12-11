<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status column to property table';
    }

    public function up(Schema $schema): void
    {
        // Add status column to property table
        $this->addSql('ALTER TABLE property ADD status VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove status column from property table
        $this->addSql('ALTER TABLE property DROP COLUMN status');
    }
}
