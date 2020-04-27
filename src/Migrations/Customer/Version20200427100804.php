<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427100804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE media_product (media_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_B29D82E4EA9FDD75 (media_id), INDEX IDX_B29D82E44584665A (product_id), PRIMARY KEY(media_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_product ADD CONSTRAINT FK_B29D82E4EA9FDD75 FOREIGN KEY (media_id) REFERENCES Media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_product ADD CONSTRAINT FK_B29D82E44584665A FOREIGN KEY (product_id) REFERENCES Product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE media_product');
    }
}
