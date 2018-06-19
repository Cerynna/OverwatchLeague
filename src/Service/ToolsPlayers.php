<?php
/**
 * Created by PhpStorm.
 * User: cerynna
 * Date: 27/04/18
 * Time: 01:15
 */

namespace App\Service;


use App\Entity\ArchiveStats;
use App\Entity\Game;
use App\Entity\GamePlayed;
use App\Entity\Matches;
use App\Entity\Player;
use App\Entity\Roster;
use App\Entity\Stages;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class ToolsPlayers
{
    private $em;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function findTeam(Player $player, Team $teamA, Team $TeamB)
    {

        if ($player->getTeam()->getAbbreviatedName() === $teamA->getAbbreviatedName()) {

            return "A";
        } elseif ($player->getTeam()->getAbbreviatedName() === $TeamB->getAbbreviatedName()) {

            return "B";
        } else {
            return $player->getName();
        }
    }

    public function archiveStats(Stages $stage, $comment = null)
    {
        $players = $this->em->getRepository(Player::class)->allOrder('ASC', 'name');
        /** @var Player $player */
        foreach ($players as $player) {
            if (!is_null($player->getStats()) and !empty($player->getStats())) {

                $archive = new ArchiveStats();

                $archiveDB = $this->em->getRepository(ArchiveStats::class)->findOneBy(['player' => $player, 'stage' => $stage]);
                if (!is_null($archiveDB) and !empty($archiveDB)) {
                    $archive = $archiveDB;
                }

                $archive->setPlayer($player);
                $archive->setStats($player->getStats());
                $archive->setComment("test - " . $stage->getWeek());
                if (!is_null($comment)) {
                    $archive->setComment($comment);
                }
                $archive->setStage($stage);
                $this->em->persist($archive);
            }

        }
        $this->em->flush();

    }


    public function getStatPlayer(Player $player)
    {
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
        $stats['ratioPrize'] = 1;
        if (!is_null($player->getStats())) {
            $stats = $player->getStats();
        }
        $statsTeam = $player->getTeam()->getStats();
        if (isset($stats["total"]) and $stats["total"] !== 0) {
            $stats['score'] = (($stats["win"] * 3) + ($stats["lose"]) + ($stats["tie"] * 2)) / $stats["total"];
            $stats['scorePoint'] = (($stats["winPoint"] * 3) + ($stats["losePoint"]) + ($stats["tiePoint"] * 2)) / $stats["total"];
            $stats['ratio'] = (($stats['score'] + $stats['scorePoint']));

            $stats['prize'] = round(Player::PRIZE * $stats['ratio'], 0);
            if ($statsTeam['teamLoseStrick'] !== 0) {
                $modulateur = 10;
                if ($statsTeam['teamLoseStrick'] > 10) {
                    $modulateur = $statsTeam['teamLoseStrick'];
                }
                $stats['bonus'] = round((Player::BONUS_PRIZE['map'] * $statsTeam['teamLoseStrick']) * (10 + $stats['ratio']), 0);
            }
            if ($statsTeam['teamWinStrick'] !== 0) {
                $modulateur = 10;
                if ($statsTeam['teamWinStrick'] > 10) {
                    $modulateur = $statsTeam['teamLoseStrick'];
                }
                $stats['bonus'] = round((Player::BONUS_PRIZE['map'] * $statsTeam['teamWinStrick']) * ($modulateur + $stats['ratio']), 0);
            }
            $stats['gain'] = round($stats['prize'] + $stats['bonus'], 0);
            $stats['prize'] = $this->roundToQuarter($stats['prize']);
            $stats['bonus'] = $this->roundToQuarter($stats['bonus']);
            $stats['gain'] = $this->roundToQuarter($stats['gain']);
            if ($stats['prize'] > 0 AND $stats['gain'] > 0) {
                $stats['ratioPrize'] = round($stats['gain'] / $stats['prize'], 2);
            } else {
                $stats['ratioPrize'] = 1;
            }

            $stats = array_merge($stats, $statsTeam);
        }


        return $stats;


    }

    /**
     * @param Roster $roster
     */
    public function getRewardRoster($roster)
    {
        /** @var Stages $currentStage */
        $currentStage = $this->em->getRepository(Stages::class)->currentStage();
        if ($roster->getStage()->getId() !== $currentStage->getId()) {
            $matches = $roster->getStage()->getMatches()->toArray();
            $allPlayers = [];
            /** @var Matches $match */
            foreach ($matches as $match) {
                $games = $match->getGames()->toArray();
                $teams[$match->getTeamA()->getAbbreviatedName()] =
                    ($match->getScores()['A'] > $match->getScores()['B']) ? true : false;
                $teams[$match->getTeamB()->getAbbreviatedName()] =
                    ($match->getScores()['B'] > $match->getScores()['A']) ? true : false;
                /** @var Game $game */
                foreach ($games as $game) {
                    $gamePlayeds = $game->getGamePlayeds()->toArray();
                    $scoreGame[$match->getTeamA()->getAbbreviatedName()] =
                        ($game->getScoreTeamA() > $game->getScoreTeamB()) ? true : false;
                    $scoreGame[$match->getTeamB()->getAbbreviatedName()] =
                        ($game->getScoreTeamB() > $game->getScoreTeamA()) ? true : false;
                    /** @var GamePlayed $gameplayed */
                    foreach ($gamePlayeds as $gameplayed) {
                        /** @var Player $player */
                        $player = $gameplayed->getPlayer();
                        $abrvTeam = $player->getTeam()->getAbbreviatedName();

                        $allPlayers[$player->getName()][$game->getIdOWL()] = [
                            "team" => $teams[$abrvTeam],
                            "map" => $scoreGame[$abrvTeam]
                        ];

                    }
                }
                unset($teams);
            }
            $reward = 0;
            $rewardTexts = [];
            /*dump($allPlayers);*/
            /** @var Player $player */
            foreach ($roster->getPlayer()->toArray() as $player) {
                if (isset($allPlayers[$player->getName()])) {
                    dump("IL A PLAY !!!!" . $player->getName());
                    $score = 0;
                    $scoreTeam = 0;
                    foreach ($allPlayers[$player->getName()] as $item) {
                        if ($item["map"] === true) {
                            $score = $score + 1;
                        } else {
                            $score = $score - 1;
                        }
                        if ($item["team"] === true) {
                            $scoreTeam = $scoreTeam + 1;
                        } else {
                            $scoreTeam = $scoreTeam - 1;
                        }
                    }
                    if ($score >= 0 && $scoreTeam >= 0) {
                        /*dump("PLAYER WIN TEAM WIN");*/
                        $txt = $player->getName() . ", a joué et a gagné. Ca Team a gagné. Tu gagne donc " . $player->getGain() . " max : " . $player->getGain();
                        array_push($rewardTexts, $txt);
                        $reward += $player->getGain();
                    } elseif ($score >= 0 && $scoreTeam <= 0) {
                        /*dump("PLAYER WIN TEAM LOSE");*/
                        $txt = $player->getName() . ", a joué et a gagné. Ca Team a perdu. Tu gagne donc " . ($player->getGain() / 2) . " max : " . $player->getGain();
                        array_push($rewardTexts, $txt);
                        $reward += ($player->getGain() / 2);
                    } elseif ($score <= 0 && $scoreTeam <= 0) {
                        $txt = $player->getName() . ", a joué et a perdu. Ca Team a perdu. Tu gagne donc 0" . " max : " . $player->getGain();
                        array_push($rewardTexts, $txt);
                        /*dump("PLAYER LOSE TEAM LOSE");*/
                    } elseif ($score <= 0 && $scoreTeam >= 0) {
                        /*dump("PLAYER LOSE TEAM WIN");*/
                        $txt = $player->getName() . ", a joué et a perdu. Ca Team a gagné. Tu gagne donc " . ($player->getGain() / 5) . " max : " . $player->getGain();
                        array_push($rewardTexts, $txt);
                        $reward += ($player->getGain() / 5);
                    }
                } else {
                    dump("IL A PAS PLAY !!!!" . $player->getName());
                    $reward += $player->getPrize();

                }
            }
            dump($reward);
            dump($rewardTexts);
        }
    }


    public function roundToQuarter($number)
    {
        return $number - ($number % 25);
    }

}