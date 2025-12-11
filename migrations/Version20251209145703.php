<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209145703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log ADD ip_address VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_FD06F647A76ED395 ON activity_log (user_id)');
        $this->addSql('ALTER TABLE property RENAME INDEX fk_8bf21cde7e3c61f9 TO IDX_8BF21CDE7E3C61F9');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL, CHANGE status status VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log DROP FOREIGN KEY FK_FD06F647A76ED395');
        $this->addSql('DROP INDEX IDX_FD06F647A76ED395 ON activity_log');
        $this->addSql('ALTER TABLE activity_log DROP ip_address');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE status status VARCHAR(20) DEFAULT \'active\' NOT NULL');
        $this->addSql('ALTER TABLE property RENAME INDEX idx_8bf21cde7e3c61f9 TO FK_8BF21CDE7E3C61F9');
    }
}
