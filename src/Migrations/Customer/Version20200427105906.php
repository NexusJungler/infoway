<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427105906 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image DROP extension, DROP ratio, DROP height, DROP width, DROP name, DROP size, DROP created_at, DROP diffusion_start, DROP diffusion_end, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_4FC2B5BBF396750 FOREIGN KEY (id) REFERENCES Media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE video DROP name, DROP extension, DROP size, DROP created_at, DROP diffusion_start, DROP diffusion_end, DROP ratio, DROP height, DROP width, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_BD06F528BF396750 FOREIGN KEY (id) REFERENCES Media (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Image DROP FOREIGN KEY FK_4FC2B5BBF396750');
        $this->addSql('ALTER TABLE Image ADD extension VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD ratio VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD height SMALLINT NOT NULL, ADD width SMALLINT NOT NULL, ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD size VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD diffusion_start DATETIME NOT NULL, ADD diffusion_end DATETIME DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE Media DROP type');
        $this->addSql('ALTER TABLE Video DROP FOREIGN KEY FK_BD06F528BF396750');
        $this->addSql('ALTER TABLE Video ADD name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD extension VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD size VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD diffusion_start DATETIME NOT NULL, ADD diffusion_end DATETIME DEFAULT NULL, ADD ratio VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD height SMALLINT NOT NULL, ADD width SMALLINT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
