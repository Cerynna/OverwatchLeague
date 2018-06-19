<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180515020639 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE roster (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_60B9ADF9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, roster_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, handle VARCHAR(255) DEFAULT NULL, home_location VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, twitch VARCHAR(255) DEFAULT NULL, youtube VARCHAR(255) DEFAULT NULL, heroes LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', player_number INT DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, headshot VARCHAR(255) DEFAULT NULL, discord VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, id_owl INT DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, stats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', prize INT DEFAULT NULL, bonus INT DEFAULT NULL, gain INT DEFAULT NULL, ratio INT DEFAULT NULL, INDEX IDX_98197A65296CD8AE (team_id), INDEX IDX_98197A6575404483 (roster_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, matches_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, id_owl INT DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, score_team_a INT DEFAULT NULL, score_team_b INT DEFAULT NULL, INDEX IDX_232B318C4B30DD19 (matches_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_player (game_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_E52CD7ADE48FD905 (game_id), INDEX IDX_E52CD7AD99E6F5DF (player_id), PRIMARY KEY(game_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, handle VARCHAR(255) DEFAULT NULL, home_location VARCHAR(255) DEFAULT NULL, primary_color VARCHAR(255) DEFAULT NULL, secondary_color VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, discord VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, youtube VARCHAR(255) DEFAULT NULL, abbreviated_name VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, id_owl INT DEFAULT NULL, twitch VARCHAR(255) DEFAULT NULL, stats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matches (id INT AUTO_INCREMENT NOT NULL, team_a_id INT DEFAULT NULL, team_b_id INT DEFAULT NULL, stages_id INT DEFAULT NULL, id_owl INT DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, scores LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_62615BAEA3FA723 (team_a_id), INDEX IDX_62615BAF88A08CD (team_b_id), INDEX IDX_62615BA8E55E70A (stages_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_played (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, game_id INT DEFAULT NULL, team VARCHAR(255) NOT NULL, INDEX IDX_11F862FC99E6F5DF (player_id), INDEX IDX_11F862FCE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE archive_stats (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, stage_id INT DEFAULT NULL, stats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', date DATETIME DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_30AA08B099E6F5DF (player_id), INDEX IDX_30AA08B02298D193 (stage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, week INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A6575404483 FOREIGN KEY (roster_id) REFERENCES roster (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C4B30DD19 FOREIGN KEY (matches_id) REFERENCES matches (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BAEA3FA723 FOREIGN KEY (team_a_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BAF88A08CD FOREIGN KEY (team_b_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BA8E55E70A FOREIGN KEY (stages_id) REFERENCES stages (id)');
        $this->addSql('ALTER TABLE game_played ADD CONSTRAINT FK_11F862FC99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_played ADD CONSTRAINT FK_11F862FCE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE archive_stats ADD CONSTRAINT FK_30AA08B099E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE archive_stats ADD CONSTRAINT FK_30AA08B02298D193 FOREIGN KEY (stage_id) REFERENCES stages (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A6575404483');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD99E6F5DF');
        $this->addSql('ALTER TABLE game_played DROP FOREIGN KEY FK_11F862FC99E6F5DF');
        $this->addSql('ALTER TABLE archive_stats DROP FOREIGN KEY FK_30AA08B099E6F5DF');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADE48FD905');
        $this->addSql('ALTER TABLE game_played DROP FOREIGN KEY FK_11F862FCE48FD905');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65296CD8AE');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BAEA3FA723');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BAF88A08CD');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C4B30DD19');
        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF9A76ED395');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BA8E55E70A');
        $this->addSql('ALTER TABLE archive_stats DROP FOREIGN KEY FK_30AA08B02298D193');
        $this->addSql('DROP TABLE roster');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_player');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE matches');
        $this->addSql('DROP TABLE game_played');
        $this->addSql('DROP TABLE archive_stats');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE stages');
    }
}
