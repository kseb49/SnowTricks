<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818095831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE figures (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, groups_id INT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, INDEX IDX_ABF1009A98333A1E (users_id), INDEX IDX_ABF1009A2EE7F9F3 (groups_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE figures_images (figures_id INT NOT NULL, images_id INT NOT NULL, INDEX IDX_BDAA97725C7F3A37 (figures_id), INDEX IDX_BDAA9772D44F05E5 (images_id), PRIMARY KEY(figures_id, images_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `groups` (id INT AUTO_INCREMENT NOT NULL, group_name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, content LONGTEXT NOT NULL, message_date DATETIME NOT NULL, INDEX IDX_DB021E9698333A1E (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, photo VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos (id INT AUTO_INCREMENT NOT NULL, figures_id INT NOT NULL, src VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_29AA64326044248D (src), INDEX IDX_29AA6432A27684AD (figures_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE figures ADD CONSTRAINT FK_ABF1009A98333A1E FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE figures ADD CONSTRAINT FK_ABF1009A2EE7F9F3 FOREIGN KEY (groups_id) REFERENCES `groups` (id)');
        $this->addSql('ALTER TABLE figures_images ADD CONSTRAINT FK_BDAA97725C7F3A37 FOREIGN KEY (figures_id) REFERENCES figures (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE figures_images ADD CONSTRAINT FK_BDAA9772D44F05E5 FOREIGN KEY (images_id) REFERENCES images (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9698333A1E FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432A27684AD FOREIGN KEY (figures_id) REFERENCES figures (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figures DROP FOREIGN KEY FK_ABF1009A98333A1E');
        $this->addSql('ALTER TABLE figures DROP FOREIGN KEY FK_ABF1009A2EE7F9F3');
        $this->addSql('ALTER TABLE figures_images DROP FOREIGN KEY FK_BDAA97725C7F3A37');
        $this->addSql('ALTER TABLE figures_images DROP FOREIGN KEY FK_BDAA9772D44F05E5');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E9698333A1E');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432A27684AD');
        $this->addSql('DROP TABLE figures');
        $this->addSql('DROP TABLE figures_images');
        $this->addSql('DROP TABLE `groups`');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE videos');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
