<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330125457 extends AbstractMigration
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
        $this->addSql('CREATE TABLE company_piece (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, name VARCHAR(45) NOT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, phone_number VARCHAR(30) NOT NULL, description VARCHAR(255) DEFAULT NULL, city VARCHAR(80) NOT NULL, country INT NOT NULL, logoName VARCHAR(255) NOT NULL, INDEX IDX_33BA946C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_piece_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Image (id INT AUTO_INCREMENT NOT NULL, media_id INT NOT NULL, extension VARCHAR(255) NOT NULL, ratio VARCHAR(255) NOT NULL, height VARCHAR(255) NOT NULL, width VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4FC2B5BEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Media (id INT AUTO_INCREMENT NOT NULL, fileName VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Product (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE RolePermissions (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, permission_id INT NOT NULL, INDEX IDX_CFCBE772D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, adress VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, phoneNumber VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, country_id INT DEFAULT NULL, timezone_id INT DEFAULT NULL, customer_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3BC4F1635E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Video (id INT AUTO_INCREMENT NOT NULL, media_id INT NOT NULL, encodage VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BD06F528EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_piece ADD CONSTRAINT FK_33BA946C54C8C93 FOREIGN KEY (type_id) REFERENCES company_piece_type (id)');
        $this->addSql('ALTER TABLE Image ADD CONSTRAINT FK_4FC2B5BEA9FDD75 FOREIGN KEY (media_id) REFERENCES Media (id)');
        $this->addSql('ALTER TABLE RolePermissions ADD CONSTRAINT FK_CFCBE772D60322AC FOREIGN KEY (role_id) REFERENCES Role (id)');
        $this->addSql('ALTER TABLE Video ADD CONSTRAINT FK_BD06F528EA9FDD75 FOREIGN KEY (media_id) REFERENCES Media (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company_piece DROP FOREIGN KEY FK_33BA946C54C8C93');
        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5BEA9FDD75');
        $this->addSql('ALTER TABLE Video DROP FOREIGN KEY FK_BD06F528EA9FDD75');
        $this->addSql('ALTER TABLE RolePermissions DROP FOREIGN KEY FK_CFCBE772D60322AC');
        $this->addSql('DROP TABLE Category');
        $this->addSql('DROP TABLE company_piece');
        $this->addSql('DROP TABLE company_piece_type');
        $this->addSql('DROP TABLE Image');
        $this->addSql('DROP TABLE Media');
        $this->addSql('DROP TABLE Product');
        $this->addSql('DROP TABLE Role');
        $this->addSql('DROP TABLE RolePermissions');
        $this->addSql('DROP TABLE Site');
        $this->addSql('DROP TABLE Tag');
        $this->addSql('DROP TABLE Video');
    }
}
