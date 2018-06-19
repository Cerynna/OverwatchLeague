<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\GamePlayed;
use App\Entity\Matches;
use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function playerByMatch($arrMatches)
    {
        $playerWeek = [];
        /** @var Matches $match */
        foreach ($arrMatches as $matches) {
            $query = $this->createQueryBuilder('g');
            $query->where('g.matches = :idMatches')
                ->setParameter('idMatches', $matches);
            $result = $query->getQuery()->getResult();

            $tagTeamA = $matches->getTeamA()->getAbbreviatedName();
            $tagTeamB = $matches->getTeamB()->getAbbreviatedName();
            $scoreMatch = $matches->getScores();


            /** @var Team $teamA */
            $teamA = $matches->getTeamA();
            /** @var Team $teamB */
            $teamB = $matches->getTeamB();


            $statsTeamA['teamWin'] = 0;
            $statsTeamA['teamTie'] = 0;
            $statsTeamA['teamLose'] = 0;
            $statsTeamA['teamWinStrick'] = 0;
            $statsTeamA['teamLoseStrick'] = 0;
            $statsTeamA['teamTotal'] = 0;

            $statsTeamB['teamWin'] = 0;
            $statsTeamB['teamTie'] = 0;
            $statsTeamB['teamLose'] = 0;
            $statsTeamB['teamWinStrick'] = 0;
            $statsTeamB['teamLoseStrick'] = 0;
            $statsTeamB['teamTotal'] = 0;

            if (!is_null($teamA->getStats()) and !empty($teamA->getStats())) {
                $statsTeamA = $teamA->getStats();
            }
            if (!is_null($teamB->getStats()) and !empty($teamB->getStats())) {
                $statsTeamB = $teamB->getStats();
            }


            if ($scoreMatch["A"] > $scoreMatch["B"]) {
                $statsTeamA['teamWin'] = $statsTeamA['teamWin'] + 1;
                $statsTeamB['teamLose'] = $statsTeamB['teamLose'] + 1;

                $statsTeamA['teamWinStrick'] = $statsTeamA['teamWinStrick'] + 1;
                $statsTeamB['teamLoseStrick'] = $statsTeamB['teamLoseStrick'] + 1;
                if ($statsTeamB['teamWinStrick'] > 0) {
                    $statsTeamB['teamWinStrick'] = 0;
                }
                if ($statsTeamA['teamLoseStrick'] > 0) {
                    $statsTeamA['teamLoseStrick'] = 0;
                }


            }
            if ($scoreMatch["A"] < $scoreMatch["B"]) {
                $statsTeamB['teamWin'] = $statsTeamB['teamWin'] + 1;
                $statsTeamA['teamLose'] = $statsTeamA['teamLose'] + 1;

                $statsTeamB['teamWinStrick'] = $statsTeamB['teamWinStrick'] + 1;
                $statsTeamA['teamLoseStrick'] = $statsTeamA['teamLoseStrick'] + 1;

                if ($statsTeamB['teamLoseStrick'] > 0) {
                    $statsTeamB['teamLoseStrick'] = 0;
                }
                if ($statsTeamA['teamWinStrick'] > 0) {
                    $statsTeamA['teamWinStrick'] = 0;
                }

            }
            if ($scoreMatch["A"] === $scoreMatch["B"]) {
                $statsTeamA['teamTie'] = $statsTeamA['teamTie'] + 1;
            }
            $statsTeamA['teamTotal'] = $statsTeamA['teamTotal'] + 1;
            $statsTeamB['teamTotal'] = $statsTeamB['teamTotal'] + 1;


            $teamA->setStats($statsTeamA);
            $teamB->setStats($statsTeamB);


            /** @var Game $game */
            foreach ($result as $game) {


                /** @var GamePlayed $player */
                foreach ($game->getGamePlayeds() as $gamePlayed) {
                    /** @var Player $player */
                    $player = $gamePlayed->getPlayer();

                    $statinDB = $player->getStats();

                    $stats["win"] = 0;
                    $stats["tie"] = 0;
                    $stats["lose"] = 0;
                    $stats["winPoint"] = 0;
                    $stats["losePoint"] = 0;
                    $stats["tiePoint"] = 0;
                    $stats["total"] = 0;
                    $stats["totalPoint"] = 0;
                    $stats['ratio'] = 1;
                    $stats['score'] = 0;
                    $stats['scorePoint'] = 0;
                    $stats['prize'] = Player::PRIZE;
                    $stats['bonus'] = Player::BONUS_PRIZE['map'] * 4;
                    $stats['gain'] = Player::PRIZE + Player::BONUS_PRIZE['map'] * 4;
                    if (!is_null($statinDB) and !empty($statinDB)) {
                        $stats = $statinDB;
                    }
                    if (isset($playerWeek[$player->getIdOWL()]) and !empty($playerWeek[$player->getIdOWL()])) {
                        $stats = $playerWeek[$player->getIdOWL()]->getStats();
                    }


                    if ($player->getTeam()->getAbbreviatedName() === $tagTeamA) {

                        if ($scoreMatch["A"] > $scoreMatch["B"]) {
                            $stats["win"] = $stats["win"] + 1;
                            $stats["winPoint"] = $stats["winPoint"] + $game->getScoreTeamA();
                        }
                        if ($scoreMatch["A"] < $scoreMatch["B"]) {
                            $stats["lose"] = $stats["lose"] + 1;
                            $stats["losePoint"] = $stats["losePoint"] + $game->getScoreTeamA();
                        }
                        if ($scoreMatch["A"] === $scoreMatch["B"]) {
                            $stats["tie"] = $stats["lose"] + 1;
                            $stats["tiePoint"] = $stats["tiePoint"] + $game->getScoreTeamA();
                        }
                        $stats["totalPoint"] = $stats["totalPoint"] + $game->getScoreTeamA();
                    } elseif ($player->getTeam()->getAbbreviatedName() === $tagTeamB) {
                        if ($scoreMatch["A"] < $scoreMatch["B"]) {
                            $stats["win"] = $stats["win"] + 1;
                            $stats["winPoint"] = $stats["winPoint"] + $game->getScoreTeamB();
                        }
                        if ($scoreMatch["A"] > $scoreMatch["B"]) {
                            $stats["lose"] = $stats["lose"] + 1;
                            $stats["losePoint"] = $stats["losePoint"] + $game->getScoreTeamB();
                        }
                        if ($scoreMatch["A"] === $scoreMatch["B"]) {
                            $stats["tie"] = $stats["lose"] + 1;
                            $stats["tiePoint"] = $stats["tiePoint"] + $game->getScoreTeamB();
                        }

                        $stats["totalPoint"] = $stats["totalPoint"] + $game->getScoreTeamB();
                    }
                    $stats["total"] = $stats["total"] + 1;


                    $player->setStats($stats);

                    $playerWeek[$player->getIdOWL()] = $player;


                    $this->getEntityManager()->persist($player);
                    $this->getEntityManager()->flush();
                }

            }

        }


        return $playerWeek;

    }
//    /**
//     * @return Game[] Returns an array of Game objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
