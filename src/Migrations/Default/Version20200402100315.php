<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200402100315 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Contact (id INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, phoneNumber VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_contact (customer_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_50BF42869395C3F3 (customer_id), INDEX IDX_50BF4286E7A1254A (contact_id), PRIMARY KEY(customer_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_contact ADD CONSTRAINT FK_50BF42869395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_contact ADD CONSTRAINT FK_50BF4286E7A1254A FOREIGN KEY (contact_id) REFERENCES Contact (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE customercontacts');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_contact DROP FOREIGN KEY FK_50BF4286E7A1254A');
        $this->addSql('CREATE TABLE customercontacts (id INT AUTO_INCREMENT NOT NULL, customer INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_B59C499B81398E09 (customer), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE customercontacts ADD CONSTRAINT FK_B59C499B81398E09 FOREIGN KEY (customer) REFERENCES customer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE Contact');
        $this->addSql('DROP TABLE customer_contact');
    }
}
