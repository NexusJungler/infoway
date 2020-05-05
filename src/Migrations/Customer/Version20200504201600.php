<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504201600 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Category (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Image (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, diffusion_start DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, diffusion_end DATETIME NOT NULL, ratio VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, height SMALLINT NOT NULL, width SMALLINT NOT NULL, type VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_ABED8E085E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_tag (media_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_48D8C57EEA9FDD75 (media_id), INDEX IDX_48D8C57EBAD26311 (tag_id), PRIMARY KEY(media_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_product (media_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_B29D82E4EA9FDD75 (media_id), INDEX IDX_B29D82E44584665A (product_id), PRIMARY KEY(media_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Product (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE RolePermissions (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, permission_id INT NOT NULL, INDEX IDX_CFCBE772D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, adress VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, phoneNumber VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, country_id INT DEFAULT NULL, timezone_id INT DEFAULT NULL, customer_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Synchro (id INT AUTO_INCREMENT NOT NULL, directory VARCHAR(255) NOT NULL, nbr_files INT NOT NULL, orientation VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SynchroPlaylist (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3BC4F1635E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Video (id INT NOT NULL, format VARCHAR(255) NOT NULL, sample_size VARCHAR(255) NOT NULL, encoder VARCHAR(255) NOT NULL, video_codec VARCHAR(255) NOT NULL, video_codec_level VARCHAR(255) NOT NULL, video_frequence VARCHAR(255) NOT NULL, video_frame SMALLINT NOT NULL, video_debit VARCHAR(255) NOT NULL, audio_codec VARCHAR(255) NOT NULL, audio_frame SMALLINT DEFAULT NULL, audio_debit VARCHAR(255) NOT NULL, audio_frequence VARCHAR(255) NOT NULL, audio_channel INT DEFAULT NULL, duration VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Image ADD CONSTRAINT FK_4FC2B5BBF396750 FOREIGN KEY (id) REFERENCES Media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_tag ADD CONSTRAINT FK_48D8C57EEA9FDD75 FOREIGN KEY (media_id) REFERENCES Media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_tag ADD CONSTRAINT FK_48D8C57EBAD26311 FOREIGN KEY (tag_id) REFERENCES Tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_product ADD CONSTRAINT FK_B29D82E4EA9FDD75 FOREIGN KEY (media_id) REFERENCES Media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_product ADD CONSTRAINT FK_B29D82E44584665A FOREIGN KEY (product_id) REFERENCES Product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE RolePermissions ADD CONSTRAINT FK_CFCBE772D60322AC FOREIGN KEY (role_id) REFERENCES Role (id)');
        $this->addSql('ALTER TABLE Video ADD CONSTRAINT FK_BD06F528BF396750 FOREIGN KEY (id) REFERENCES Media (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5BBF396750');
        $this->addSql('ALTER TABLE media_tag DROP FOREIGN KEY FK_48D8C57EEA9FDD75');
        $this->addSql('ALTER TABLE media_product DROP FOREIGN KEY FK_B29D82E4EA9FDD75');
        $this->addSql('ALTER TABLE Video DROP FOREIGN KEY FK_BD06F528BF396750');
        $this->addSql('ALTER TABLE media_product DROP FOREIGN KEY FK_B29D82E44584665A');
        $this->addSql('ALTER TABLE RolePermissions DROP FOREIGN KEY FK_CFCBE772D60322AC');
        $this->addSql('ALTER TABLE media_tag DROP FOREIGN KEY FK_48D8C57EBAD26311');
        $this->addSql('DROP TABLE Category');
        $this->addSql('DROP TABLE Image');
        $this->addSql('DROP TABLE Media');
        $this->addSql('DROP TABLE media_tag');
        $this->addSql('DROP TABLE media_product');
        $this->addSql('DROP TABLE Product');
        $this->addSql('DROP TABLE Role');
        $this->addSql('DROP TABLE RolePermissions');
        $this->addSql('DROP TABLE Site');
        $this->addSql('DROP TABLE Synchro');
        $this->addSql('DROP TABLE SynchroPlaylist');
        $this->addSql('DROP TABLE Tag');
        $this->addSql('DROP TABLE Video');
    }
}
