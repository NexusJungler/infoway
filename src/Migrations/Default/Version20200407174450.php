<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200407174450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, perimeter_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, account_confirmation_token VARCHAR(255) DEFAULT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, requested_password_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, account_confirmed_at DATETIME DEFAULT NULL, activated TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2DA17977E7927C74 (email), INDEX IDX_2DA1797777570A4C (perimeter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_customers (user_id INT NOT NULL, customer_id INT NOT NULL, INDEX IDX_42E34C0A76ED395 (user_id), INDEX IDX_42E34C09395C3F3 (customer_id), PRIMARY KEY(user_id, customer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Action (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_406089A45E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Contact (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, phoneNumber VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_83DFDFA49395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9CCEF0FA5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Customer (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, timezone_id INT DEFAULT NULL, name VARCHAR(60) NOT NULL, logo VARCHAR(60) NOT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(60) DEFAULT NULL, city VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_784FEC5F5E237E06 (name), UNIQUE INDEX UNIQ_784FEC5FE48E9A13 (logo), INDEX IDX_784FEC5FF92F3E70 (country_id), INDEX IDX_784FEC5F3FE997DE (timezone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Perimeter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, feature_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E04992AA60E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F75B25545E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6F7DF886D60322AC (role_id), INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_347307E65E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timezone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3701B2975E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_permissions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, permission_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, feature_id INT DEFAULT NULL, INDEX IDX_DA58F09DA76ED395 (user_id), INDEX IDX_DA58F09DFED90CCA (permission_id), INDEX IDX_DA58F09D9395C3F3 (customer_id), INDEX IDX_DA58F09D60E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, role_id INT NOT NULL, INDEX IDX_54FCD59FA76ED395 (user_id), INDEX IDX_54FCD59F9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_site (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, site_id INT NOT NULL, INDEX IDX_13C2452DA76ED395 (user_id), INDEX IDX_13C2452D9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA1797777570A4C FOREIGN KEY (perimeter_id) REFERENCES Perimeter (id)');
        $this->addSql('ALTER TABLE users_customers ADD CONSTRAINT FK_42E34C0A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_customers ADD CONSTRAINT FK_42E34C09395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Contact ADD CONSTRAINT FK_83DFDFA49395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id)');
        $this->addSql('ALTER TABLE Customer ADD CONSTRAINT FK_784FEC5FF92F3E70 FOREIGN KEY (country_id) REFERENCES Country (id)');
        $this->addSql('ALTER TABLE Customer ADD CONSTRAINT FK_784FEC5F3FE997DE FOREIGN KEY (timezone_id) REFERENCES timezone (id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886D60322AC FOREIGN KEY (role_id) REFERENCES Role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_permissions ADD CONSTRAINT FK_DA58F09DA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE users_permissions ADD CONSTRAINT FK_DA58F09DFED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('ALTER TABLE users_permissions ADD CONSTRAINT FK_DA58F09D9395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id)');
        $this->addSql('ALTER TABLE users_permissions ADD CONSTRAINT FK_DA58F09D60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F9395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id)');
        $this->addSql('ALTER TABLE user_site ADD CONSTRAINT FK_13C2452DA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE user_site ADD CONSTRAINT FK_13C2452D9395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_customers DROP FOREIGN KEY FK_42E34C0A76ED395');
        $this->addSql('ALTER TABLE users_permissions DROP FOREIGN KEY FK_DA58F09DA76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('ALTER TABLE user_site DROP FOREIGN KEY FK_13C2452DA76ED395');
        $this->addSql('ALTER TABLE Customer DROP FOREIGN KEY FK_784FEC5FF92F3E70');
        $this->addSql('ALTER TABLE users_customers DROP FOREIGN KEY FK_42E34C09395C3F3');
        $this->addSql('ALTER TABLE Contact DROP FOREIGN KEY FK_83DFDFA49395C3F3');
        $this->addSql('ALTER TABLE users_permissions DROP FOREIGN KEY FK_DA58F09D9395C3F3');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F9395C3F3');
        $this->addSql('ALTER TABLE user_site DROP FOREIGN KEY FK_13C2452D9395C3F3');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AA60E4B879');
        $this->addSql('ALTER TABLE users_permissions DROP FOREIGN KEY FK_DA58F09D60E4B879');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA1797777570A4C');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE users_permissions DROP FOREIGN KEY FK_DA58F09DFED90CCA');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886D60322AC');
        $this->addSql('ALTER TABLE Customer DROP FOREIGN KEY FK_784FEC5F3FE997DE');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE users_customers');
        $this->addSql('DROP TABLE Action');
        $this->addSql('DROP TABLE Contact');
        $this->addSql('DROP TABLE Country');
        $this->addSql('DROP TABLE Customer');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE Perimeter');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE Role');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE Subject');
        $this->addSql('DROP TABLE timezone');
        $this->addSql('DROP TABLE users_permissions');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user_site');
    }
}
