<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427110159 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video ADD sample_size VARCHAR(255) NOT NULL, ADD video_codec VARCHAR(255) NOT NULL, ADD video_codec_level VARCHAR(255) NOT NULL, ADD video_frequence VARCHAR(255) NOT NULL, ADD video_debit VARCHAR(255) NOT NULL, ADD audio_codec VARCHAR(255) NOT NULL, ADD audio_debit VARCHAR(255) NOT NULL, ADD audio_frequence VARCHAR(255) NOT NULL, DROP sampleSize, DROP videoCodec, DROP videoCodecLevel, DROP videoFrequence, DROP videoDebit, DROP audioCodec, DROP audioDebit, DROP audioFrequence, CHANGE videoframe video_frame SMALLINT NOT NULL, CHANGE audioframe audio_frame SMALLINT DEFAULT NULL, CHANGE audiochannel audio_channel INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Video ADD sampleSize VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD videoCodec VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD videoCodecLevel VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD videoFrequence VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD videoDebit VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD audioCodec VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD audioDebit VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD audioFrequence VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP sample_size, DROP video_codec, DROP video_codec_level, DROP video_frequence, DROP video_debit, DROP audio_codec, DROP audio_debit, DROP audio_frequence, CHANGE video_frame videoFrame SMALLINT NOT NULL, CHANGE audio_frame audioFrame SMALLINT DEFAULT NULL, CHANGE audio_channel audioChannel INT DEFAULT NULL');
    }
}
