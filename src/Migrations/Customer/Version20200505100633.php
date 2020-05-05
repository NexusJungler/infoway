<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200505100633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CheckoutProduct (id INT AUTO_INCREMENT NOT NULL, checkoutsystem INT DEFAULT NULL, product INT DEFAULT NULL, system_product_id INT NOT NULL, INDEX IDX_BF6DE17722F55B31 (checkoutsystem), INDEX IDX_BF6DE177D34A04AD (product), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CheckoutSystem (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Criterion (id INT AUTO_INCREMENT NOT NULL, list_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, position INT NOT NULL, selected TINYINT(1) NOT NULL, INDEX IDX_FE73A0D23DAE168B (list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CriterionList (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, multiple TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Date (id INT AUTO_INCREMENT NOT NULL, value DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ExpectedChange (id INT AUTO_INCREMENT NOT NULL, entity INT NOT NULL, instance INT NOT NULL, datas JSON NOT NULL, requestedAt DATETIME NOT NULL, expectedAt_id INT NOT NULL, INDEX IDX_77CB450AE8D31317 (expectedAt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MainPrice (id INT AUTO_INCREMENT NOT NULL, factory_id INT NOT NULL, product_id INT DEFAULT NULL, day_value VARCHAR(50) NOT NULL, night_value VARCHAR(50) NOT NULL, INDEX IDX_346ECD07C7AF27D2 (factory_id), INDEX IDX_346ECD074584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE PricesFactory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE PriceType (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E3A6E39C4584665A (product_id), INDEX IDX_E3A6E39CBAD26311 (tag_id), PRIMARY KEY(product_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_criterions (product_id INT NOT NULL, criterion_id INT NOT NULL, INDEX IDX_EE2FF49D4584665A (product_id), INDEX IDX_EE2FF49D97766307 (criterion_id), PRIMARY KEY(product_id, criterion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_allergen (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, allergen_id INT NOT NULL, INDEX IDX_EE0F62594584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE TagCategory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, required TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CheckoutProduct ADD CONSTRAINT FK_BF6DE17722F55B31 FOREIGN KEY (checkoutsystem) REFERENCES CheckoutSystem (id)');
        $this->addSql('ALTER TABLE CheckoutProduct ADD CONSTRAINT FK_BF6DE177D34A04AD FOREIGN KEY (product) REFERENCES Product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Criterion ADD CONSTRAINT FK_FE73A0D23DAE168B FOREIGN KEY (list_id) REFERENCES CriterionList (id)');
        $this->addSql('ALTER TABLE ExpectedChange ADD CONSTRAINT FK_77CB450AE8D31317 FOREIGN KEY (expectedAt_id) REFERENCES Date (id)');
        $this->addSql('ALTER TABLE MainPrice ADD CONSTRAINT FK_346ECD07C7AF27D2 FOREIGN KEY (factory_id) REFERENCES PricesFactory (id)');
        $this->addSql('ALTER TABLE MainPrice ADD CONSTRAINT FK_346ECD074584665A FOREIGN KEY (product_id) REFERENCES Product (id)');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39C4584665A FOREIGN KEY (product_id) REFERENCES Product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39CBAD26311 FOREIGN KEY (tag_id) REFERENCES Tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_criterions ADD CONSTRAINT FK_EE2FF49D4584665A FOREIGN KEY (product_id) REFERENCES Product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_criterions ADD CONSTRAINT FK_EE2FF49D97766307 FOREIGN KEY (criterion_id) REFERENCES Criterion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_allergen ADD CONSTRAINT FK_EE0F62594584665A FOREIGN KEY (product_id) REFERENCES Product (id)');
        $this->addSql('ALTER TABLE category ADD name VARCHAR(255) NOT NULL, ADD note VARCHAR(255) DEFAULT NULL, ADD createdAt DATE NOT NULL, ADD logo VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD category_id INT DEFAULT NULL, ADD price_type_id INT NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD amount VARCHAR(50) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD note VARCHAR(255) DEFAULT NULL, ADD logo VARCHAR(100) DEFAULT NULL, ADD createdAt DATE NOT NULL, ADD start DATE DEFAULT NULL, ADD end DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_1CF73D3112469DE2 FOREIGN KEY (category_id) REFERENCES Category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_1CF73D31AE6A44CF FOREIGN KEY (price_type_id) REFERENCES PriceType (id)');
        $this->addSql('CREATE INDEX IDX_1CF73D3112469DE2 ON product (category_id)');
        $this->addSql('CREATE INDEX IDX_1CF73D31AE6A44CF ON product (price_type_id)');
        $this->addSql('ALTER TABLE tag ADD description LONGTEXT NOT NULL, ADD color VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BC4F163665648E9 ON tag (color)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CheckoutProduct DROP FOREIGN KEY FK_BF6DE17722F55B31');
        $this->addSql('ALTER TABLE products_criterions DROP FOREIGN KEY FK_EE2FF49D97766307');
        $this->addSql('ALTER TABLE Criterion DROP FOREIGN KEY FK_FE73A0D23DAE168B');
        $this->addSql('ALTER TABLE ExpectedChange DROP FOREIGN KEY FK_77CB450AE8D31317');
        $this->addSql('ALTER TABLE MainPrice DROP FOREIGN KEY FK_346ECD07C7AF27D2');
        $this->addSql('ALTER TABLE Product DROP FOREIGN KEY FK_1CF73D31AE6A44CF');
        $this->addSql('DROP TABLE CheckoutProduct');
        $this->addSql('DROP TABLE CheckoutSystem');
        $this->addSql('DROP TABLE Criterion');
        $this->addSql('DROP TABLE CriterionList');
        $this->addSql('DROP TABLE Date');
        $this->addSql('DROP TABLE ExpectedChange');
        $this->addSql('DROP TABLE MainPrice');
        $this->addSql('DROP TABLE PricesFactory');
        $this->addSql('DROP TABLE PriceType');
        $this->addSql('DROP TABLE product_tag');
        $this->addSql('DROP TABLE products_criterions');
        $this->addSql('DROP TABLE product_allergen');
        $this->addSql('DROP TABLE TagCategory');
        $this->addSql('ALTER TABLE Category DROP name, DROP note, DROP createdAt, DROP logo');
        $this->addSql('ALTER TABLE Product DROP FOREIGN KEY FK_1CF73D3112469DE2');
        $this->addSql('DROP INDEX IDX_1CF73D3112469DE2 ON Product');
        $this->addSql('DROP INDEX IDX_1CF73D31AE6A44CF ON Product');
        $this->addSql('ALTER TABLE Product DROP category_id, DROP price_type_id, DROP name, DROP amount, DROP description, DROP note, DROP logo, DROP createdAt, DROP start, DROP end');
        $this->addSql('DROP INDEX UNIQ_3BC4F163665648E9 ON Tag');
        $this->addSql('ALTER TABLE Tag DROP description, DROP color');
    }
}
