<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013154857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD first_name VARCHAR(100) NOT NULL, ADD last_name VARCHAR(100) NOT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD property_interest VARCHAR(255) DEFAULT NULL, ADD purchase_timeframe VARCHAR(255) DEFAULT NULL, ADD country VARCHAR(100) DEFAULT NULL, ADD city VARCHAR(100) DEFAULT NULL, DROP name, DROP subject, CHANGE email email VARCHAR(180) NOT NULL, CHANGE message message LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD name VARCHAR(255) NOT NULL, ADD subject VARCHAR(255) NOT NULL, DROP first_name, DROP last_name, DROP phone, DROP property_interest, DROP purchase_timeframe, DROP country, DROP city, CHANGE email email VARCHAR(255) NOT NULL, CHANGE message message LONGTEXT NOT NULL');
    }
}
