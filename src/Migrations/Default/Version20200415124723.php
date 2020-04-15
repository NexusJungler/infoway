<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415124723 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ffmpeg_tasks ADD customer_id INT NOT NULL, DROP customer');
        $this->addSql('ALTER TABLE ffmpeg_tasks ADD CONSTRAINT FK_6DD9EBF59395C3F3 FOREIGN KEY (customer_id) REFERENCES Customer (id)');
        $this->addSql('CREATE INDEX IDX_6DD9EBF59395C3F3 ON ffmpeg_tasks (customer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ffmpeg_tasks DROP FOREIGN KEY FK_6DD9EBF59395C3F3');
        $this->addSql('DROP INDEX IDX_6DD9EBF59395C3F3 ON ffmpeg_tasks');
        $this->addSql('ALTER TABLE ffmpeg_tasks ADD customer SMALLINT NOT NULL, DROP customer_id');
    }
}
