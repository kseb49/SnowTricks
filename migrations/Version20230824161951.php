<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824161951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ABF1009A5E237E06 ON figures (name)');
        $this->addSql('ALTER TABLE figures RENAME INDEX idx_abf1009a98333a1e TO IDX_ABF1009A67B3B43D');
        $this->addSql('ALTER TABLE figures RENAME INDEX idx_abf1009a2ee7f9f3 TO IDX_ABF1009AF373DCF');
        $this->addSql('ALTER TABLE users CHANGE photo photo VARCHAR(100) DEFAULT \'snowboarder-310459.png\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E95E237E06 ON users (name)');
        $this->addSql('ALTER TABLE videos CHANGE figures_id figures_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos RENAME INDEX idx_29aa6432a27684ad TO IDX_29AA64325C7F3A37');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
      
        $this->addSql('DROP INDEX UNIQ_ABF1009A5E237E06 ON figures');
        $this->addSql('ALTER TABLE figures RENAME INDEX idx_abf1009a67b3b43d TO IDX_ABF1009A98333A1E');
        $this->addSql('ALTER TABLE figures RENAME INDEX idx_abf1009af373dcf TO IDX_ABF1009A2EE7F9F3');
        $this->addSql('DROP INDEX UNIQ_1483A5E95E237E06 ON users');
        $this->addSql('ALTER TABLE users CHANGE photo photo VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE videos CHANGE figures_id figures_id INT NOT NULL');
        $this->addSql('ALTER TABLE videos RENAME INDEX idx_29aa64325c7f3a37 TO IDX_29AA6432A27684AD');
    }
}
