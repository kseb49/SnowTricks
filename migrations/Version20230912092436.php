<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912092436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messages ADD figures_id INT NOT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E965C7F3A37 FOREIGN KEY (figures_id) REFERENCES figures (id)');
        $this->addSql('CREATE INDEX IDX_DB021E965C7F3A37 ON messages (figures_id)');
        $this->addSql('ALTER TABLE messages RENAME INDEX idx_db021e9698333a1e TO IDX_DB021E9667B3B43D');
        $this->addSql('ALTER TABLE users CHANGE confirmation_date confirmation_date DATETIME DEFAULT NULL, CHANGE send_link send_link DATETIME DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E965C7F3A37');
        $this->addSql('DROP INDEX IDX_DB021E965C7F3A37 ON messages');
        $this->addSql('ALTER TABLE messages DROP figures_id');
        $this->addSql('ALTER TABLE messages RENAME INDEX idx_db021e9667b3b43d TO IDX_DB021E9698333A1E');
        $this->addSql('ALTER TABLE users CHANGE confirmation_date confirmation_date DATETIME DEFAULT NULL, CHANGE send_link send_link DATETIME DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL');
    }
}
