<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create transaction table';
    }

    public function up(Schema $schema): void
    {
        // Create transaction table
        $this->addSql('CREATE TABLE `transaction` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `property_id` INT NOT NULL,
            `buyer_id` INT NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `amount` DECIMAL(10, 2) NOT NULL,
            `created_at` DATETIME NOT NULL,
            `notes` LONGTEXT DEFAULT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (`property_id`) REFERENCES `property`(`id`),
            FOREIGN KEY (`buyer_id`) REFERENCES `user`(`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    public function down(Schema $schema): void
    {
        // Drop transaction table
        $this->addSql('DROP TABLE IF EXISTS transaction');
    }
}
