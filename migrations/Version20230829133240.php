<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829133240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE figures_videos (figures_id INT NOT NULL, videos_id INT NOT NULL, INDEX IDX_741F4D2A5C7F3A37 (figures_id), INDEX IDX_741F4D2A763C10B2 (videos_id), PRIMARY KEY(figures_id, videos_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE figures_videos ADD CONSTRAINT FK_741F4D2A5C7F3A37 FOREIGN KEY (figures_id) REFERENCES figures (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE figures_videos ADD CONSTRAINT FK_741F4D2A763C10B2 FOREIGN KEY (videos_id) REFERENCES videos (id) ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432A27684AD');
        $this->addSql('DROP INDEX IDX_29AA64325C7F3A37 ON videos');
        $this->addSql('ALTER TABLE videos DROP figures_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figures_videos DROP FOREIGN KEY FK_741F4D2A5C7F3A37');
        $this->addSql('ALTER TABLE figures_videos DROP FOREIGN KEY FK_741F4D2A763C10B2');
        $this->addSql('DROP TABLE figures_videos');
        $this->addSql('ALTER TABLE videos ADD figures_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432A27684AD FOREIGN KEY (figures_id) REFERENCES figures (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_29AA64325C7F3A37 ON videos (figures_id)');
    }
}
