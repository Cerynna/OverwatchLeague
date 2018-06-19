<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180517180832 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE roster_player (roster_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_13BF7DBA75404483 (roster_id), INDEX IDX_13BF7DBA99E6F5DF (player_id), PRIMARY KEY(roster_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roster_user (roster_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_85E1FB9E75404483 (roster_id), INDEX IDX_85E1FB9EA76ED395 (user_id), PRIMARY KEY(roster_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE roster_player ADD CONSTRAINT FK_13BF7DBA75404483 FOREIGN KEY (roster_id) REFERENCES roster (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roster_player ADD CONSTRAINT FK_13BF7DBA99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roster_user ADD CONSTRAINT FK_85E1FB9E75404483 FOREIGN KEY (roster_id) REFERENCES roster (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roster_user ADD CONSTRAINT FK_85E1FB9EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF9A76ED395');
        $this->addSql('DROP INDEX IDX_60B9ADF9A76ED395 ON roster');
        $this->addSql('ALTER TABLE roster ADD last_update DATETIME DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A6575404483');
        $this->addSql('DROP INDEX IDX_98197A6575404483 ON player');
        $this->addSql('ALTER TABLE player DROP roster_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE roster_player');
        $this->addSql('DROP TABLE roster_user');
        $this->addSql('ALTER TABLE player ADD roster_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A6575404483 FOREIGN KEY (roster_id) REFERENCES roster (id)');
        $this->addSql('CREATE INDEX IDX_98197A6575404483 ON player (roster_id)');
        $this->addSql('ALTER TABLE roster ADD user_id INT DEFAULT NULL, DROP last_update');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_60B9ADF9A76ED395 ON roster (user_id)');
    }
}
