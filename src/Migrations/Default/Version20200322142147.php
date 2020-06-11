<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200322142147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Action (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_406089A45E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Customer (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, timezone_id INT NOT NULL, name VARCHAR(60) NOT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(60) DEFAULT NULL, city VARCHAR(255) NOT NULL, phone_number VARCHAR(30) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_784FEC5FF92F3E70 (country_id), INDEX IDX_784FEC5F3FE997DE (timezone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, branch VARCHAR(60) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, feature_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E04992AA60E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F75B25545E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6F7DF886D60322AC (role_id), INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_347307E65E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timezone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3701B2975E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, customer_id INT NOT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, phone_number VARCHAR(30) NOT NULL, email VARCHAR(255) NOT NULL, registration_token VARCHAR(255) DEFAULT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, company_piece INT NOT NULL, INDEX IDX_2DA17977D60322AC (role_id), INDEX IDX_2DA179779395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), INDEX IDX_472E5446FED90CCA (permission_id), PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Customer ADD CONSTRAINT FK_784FEC5FF92F3E70 FOREIGN KEY (country_id) REFERENCES Country (id)');
        $this->addSql('ALTER TABLE Customer ADD CONSTRAINT FK_784FEC5F3FE997DE FOREIGN KEY (timezone_id) REFERENCES timezone (id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886D60322AC FOREIGN KEY (role_id) REFERENCES Role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA17977D60322AC FOREIGN KEY (role_id) REFERENCES Role (id)');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA179779395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id)');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Customer DROP FOREIGN KEY FK_784FEC5FF92F3E70');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA179779395C3F3');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AA60E4B879');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446FED90CCA');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886D60322AC');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977D60322AC');
        $this->addSql('ALTER TABLE Customer DROP FOREIGN KEY FK_784FEC5F3FE997DE');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446A76ED395');
        $this->addSql('DROP TABLE Action');
        $this->addSql('DROP TABLE Country');
        $this->addSql('DROP TABLE Customer');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE Role');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE Subject');
        $this->addSql('DROP TABLE timezone');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE user_permission');
    }
}
