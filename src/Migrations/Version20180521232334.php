<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180521232334 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE roster CHANGE stage stage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF92298D193 FOREIGN KEY (stage_id) REFERENCES stages (id)');
        $this->addSql('CREATE INDEX IDX_60B9ADF92298D193 ON roster (stage_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF92298D193');
        $this->addSql('DROP INDEX IDX_60B9ADF92298D193 ON roster');
        $this->addSql('ALTER TABLE roster CHANGE stage_id stage INT DEFAULT NULL');
    }
}
