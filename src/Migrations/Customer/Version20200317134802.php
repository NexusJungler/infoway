<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200317134802 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_piece (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, name VARCHAR(45) NOT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, phone_number VARCHAR(30) NOT NULL, description VARCHAR(255) DEFAULT NULL, city VARCHAR(80) NOT NULL, country INT NOT NULL, logoName VARCHAR(255) NOT NULL, timeZone INT NOT NULL, INDEX IDX_33BA946C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_piece_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, media_id INT NOT NULL, extension VARCHAR(255) NOT NULL, ratio VARCHAR(255) NOT NULL, height VARCHAR(255) NOT NULL, width VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C53D045FEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, fileName VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, media_id INT NOT NULL, encodage VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7CC7DA2CEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_piece ADD CONSTRAINT FK_33BA946C54C8C93 FOREIGN KEY (type_id) REFERENCES company_piece_type (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company_piece DROP FOREIGN KEY FK_33BA946C54C8C93');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FEA9FDD75');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CEA9FDD75');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE company_piece');
        $this->addSql('DROP TABLE company_piece_type');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE video');
    }
}
