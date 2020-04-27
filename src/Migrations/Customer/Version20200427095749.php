<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427095749 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image CHANGE createdat created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE diffusionstart diffusion_start DATETIME NOT NULL, CHANGE diffusionend diffusion_end DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE media CHANGE createdat created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE diffusionstart diffusion_start DATETIME NOT NULL, CHANGE diffusionend diffusion_end DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE video CHANGE createdat created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE diffusionstart diffusion_start DATETIME NOT NULL, CHANGE diffusionend diffusion_end DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Image CHANGE created_at createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE diffusion_start diffusionStart DATETIME NOT NULL, CHANGE diffusion_end diffusionEnd DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Media CHANGE diffusion_start diffusionStart DATETIME NOT NULL, CHANGE created_at createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE diffusion_end diffusionEnd DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Video CHANGE created_at createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE diffusion_start diffusionStart DATETIME NOT NULL, CHANGE diffusion_end diffusionEnd DATETIME DEFAULT NULL');
    }
}
