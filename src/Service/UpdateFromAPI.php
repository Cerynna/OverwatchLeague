<?php
/**
 * Created by PhpStorm.
 * User: cerynna
 * Date: 27/04/18
 * Time: 01:15
 */

namespace App\Service;


use App\Entity\Game;
use App\Entity\GamePlayed;
use App\Entity\Matches;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateFromAPI
{

    const BASE_URL = "https://api.overwatchleague.com/";


    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ToolsPlayers
     */
    private $toolsPlayers;

    public function __construct(EntityManagerInterface $entityManager, ToolsPlayers $toolsPlayers)
    {
        $this->em = $entityManager;
        $this->toolsPlayers = $toolsPlayers;
    }

    public function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result);
    }


    public function GetMatch($data, OutputInterface $output)
    {
        if (is_array($data)) {
            foreach ($data as $oneData) {
                $this->GetMatch($oneData, $output);
            }
        } elseif ($data instanceof Matches) {
            $matchAPI = $this->CallAPI('GET', self::BASE_URL . 'matches/' . $data->getIdOWL());
            $output->writeln(
                PHP_EOL . $data->getTeamA()->getAbbreviatedName() . " - " .
                $data->getTeamB()->getAbbreviatedName() . " | " .
                $matchAPI->scores[0]->value . " - " .
                $matchAPI->scores[1]->value
            );


            $progressBar = new ProgressBar($output, count($matchAPI->games) * 12);


            foreach ($matchAPI->games as $gameAPI) {
                $game = new Game();
                $verif = $this->em->getRepository(Game::class)->findOneBy(['idOWL' => $gameAPI->id]);
                if (!is_null($verif)) {
                    $game = $verif;
                }
                if (isset($gameAPI->attributes->mapScore) and !is_null($gameAPI->attributes->mapScore)) {
                    $game->setScoreTeamA($gameAPI->attributes->mapScore->team1);
                    $game->setScoreTeamB($gameAPI->attributes->mapScore->team2);
                }
                foreach ($gameAPI->players as $playerAPI) {
                    $progressBar->advance();
                    /** @var Player $player */
                    $player = $this->em->getRepository(Player::class)->findOneBy(['idOWL' => $playerAPI->player->id]);
                    if (!is_null($player)) {
                        $gamePlayed = new GamePlayed();
                        $verif = $this->em->getRepository(GamePlayed::class)->findOneBy([
                            'player' => $player,
                            'game' => $game,
                        ]);
                        if (!is_null($verif)) {
                            $gamePlayed = $verif;
                        }
                        /*dump($player->getName());*/
                        $gamePlayed->setPlayer($player);
                        $gamePlayed->setGame($game);
                        $gamePlayed->setTeam($this->toolsPlayers->findTeam($player, $data->getTeamA(), $data->getTeamB()));
                        $game->addPlayer($player);
                        $this->em->persist($gamePlayed);
                        $player->addGamePlayed($gamePlayed);
                        $this->em->flush();
                    }
                }
                $this->em->persist($game);
            }
            $progressBar->finish();
            $data->setScores([
                "A" => $matchAPI->scores[0]->value,
                "B" => $matchAPI->scores[1]->value
            ]);
            $this->em->persist($data);
            $this->em->flush();

        } else {
            dump("NOP");
        }


    }

}