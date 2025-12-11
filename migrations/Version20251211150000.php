<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix foreign key constraints to support cascade delete
 */
final class Version20251211150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix transaction table foreign keys with cascade delete';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign keys
        $this->addSql('ALTER TABLE `transaction` DROP FOREIGN KEY `transaction_ibfk_1`');
        $this->addSql('ALTER TABLE `transaction` DROP FOREIGN KEY `transaction_ibfk_2`');

        // Add foreign keys with cascade delete
        $this->addSql('ALTER TABLE `transaction` ADD CONSTRAINT `transaction_ibfk_1` 
            FOREIGN KEY (`property_id`) REFERENCES `property`(`id`) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `transaction` ADD CONSTRAINT `transaction_ibfk_2` 
            FOREIGN KEY (`buyer_id`) REFERENCES `user`(`id`) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop new foreign keys
        $this->addSql('ALTER TABLE `transaction` DROP FOREIGN KEY `transaction_ibfk_1`');
        $this->addSql('ALTER TABLE `transaction` DROP FOREIGN KEY `transaction_ibfk_2`');

        // Restore original foreign keys without cascade
        $this->addSql('ALTER TABLE `transaction` ADD CONSTRAINT `transaction_ibfk_1` 
            FOREIGN KEY (`property_id`) REFERENCES `property`(`id`)');
        $this->addSql('ALTER TABLE `transaction` ADD CONSTRAINT `transaction_ibfk_2` 
            FOREIGN KEY (`buyer_id`) REFERENCES `user`(`id`)');
    }
}
