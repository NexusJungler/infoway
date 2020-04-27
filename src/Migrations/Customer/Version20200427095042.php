<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427095042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE media_tag (media_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_48D8C57EEA9FDD75 (media_id), INDEX IDX_48D8C57EBAD26311 (tag_id), PRIMARY KEY(media_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_tag ADD CONSTRAINT FK_48D8C57EEA9FDD75 FOREIGN KEY (media_id) REFERENCES Media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_tag ADD CONSTRAINT FK_48D8C57EBAD26311 FOREIGN KEY (tag_id) REFERENCES Tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD name VARCHAR(255) NOT NULL, ADD size VARCHAR(255) NOT NULL, ADD createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD diffusionStart DATETIME NOT NULL, ADD diffusionEnd DATETIME DEFAULT NULL, CHANGE height height SMALLINT NOT NULL, CHANGE width width SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE media ADD name VARCHAR(255) NOT NULL, ADD createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD diffusionEnd DATETIME DEFAULT NULL, ADD ratio VARCHAR(255) NOT NULL, ADD extension VARCHAR(255) NOT NULL, ADD height SMALLINT NOT NULL, ADD width SMALLINT NOT NULL, DROP fileName, DROP type, CHANGE uploaded_at diffusionStart DATETIME NOT NULL');
        $this->addSql('ALTER TABLE video ADD size VARCHAR(255) NOT NULL, ADD createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD diffusionStart DATETIME NOT NULL, ADD diffusionEnd DATETIME DEFAULT NULL, ADD ratio VARCHAR(255) NOT NULL, ADD height SMALLINT NOT NULL, ADD width SMALLINT NOT NULL, ADD format VARCHAR(255) NOT NULL, ADD sampleSize VARCHAR(255) NOT NULL, ADD encoder VARCHAR(255) NOT NULL, ADD videoCodec VARCHAR(255) NOT NULL, ADD videoCodecLevel VARCHAR(255) NOT NULL, ADD videoFrequence VARCHAR(255) NOT NULL, ADD videoFrame SMALLINT NOT NULL, ADD videoDebit VARCHAR(255) NOT NULL, ADD audioCodec VARCHAR(255) NOT NULL, ADD audioFrame SMALLINT DEFAULT NULL, ADD audioDebit VARCHAR(255) NOT NULL, ADD audioFrequence VARCHAR(255) NOT NULL, ADD audioChannel INT DEFAULT NULL, ADD duration VARCHAR(255) NOT NULL, CHANGE encodage name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE media_tag');
        $this->addSql('ALTER TABLE Image DROP name, DROP size, DROP createdAt, DROP diffusionStart, DROP diffusionEnd, CHANGE height height VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE width width VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE Media ADD fileName VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP name, DROP createdAt, DROP diffusionEnd, DROP ratio, DROP extension, DROP height, DROP width, CHANGE diffusionstart uploaded_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Video ADD encodage VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP name, DROP size, DROP createdAt, DROP diffusionStart, DROP diffusionEnd, DROP ratio, DROP height, DROP width, DROP format, DROP sampleSize, DROP encoder, DROP videoCodec, DROP videoCodecLevel, DROP videoFrequence, DROP videoFrame, DROP videoDebit, DROP audioCodec, DROP audioFrame, DROP audioDebit, DROP audioFrequence, DROP audioChannel, DROP duration');
    }
}
