<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200429125116 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_4FC2B5BEA9FDD75');
        $this->addSql('DROP INDEX UNIQ_4FC2B5BEA9FDD75 ON image');
        $this->addSql('ALTER TABLE image DROP media_id, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_4FC2B5BBF396750 FOREIGN KEY (id) REFERENCES Media (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5BBF396750');
        $this->addSql('ALTER TABLE Image ADD media_id INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE Image ADD CONSTRAINT FK_4FC2B5BEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FC2B5BEA9FDD75 ON Image (media_id)');
    }
}
