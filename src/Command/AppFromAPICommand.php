<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\GamePlayed;
use App\Entity\Matches;
use App\Entity\Player;
use App\Entity\Stages;
use App\Entity\Team;
use App\Service\ToolsPlayers;
use App\Service\UpdateFromAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppFromAPICommand extends Command
{
    protected static $defaultName = 'app:fromAPI';
    /**
     * @var UpdateFromAPI
     */
    private $fromAPI;

    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;


    /**
     * @var ToolsPlayers
     */
    private $toolsPlayers;

    public function __construct($name = null, UpdateFromAPI $fromAPI, EntityManagerInterface $entityManager, ToolsPlayers $toolsPlayers)
    {
        $this->fromAPI = $fromAPI;
        $this->em = $entityManager;
        $this->toolsPlayers = $toolsPlayers;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('dry', null, InputOption::VALUE_NONE, 'Dry Update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* $io = new SymfonyStyle($input, $output);
         $arg1 = $input->getArgument('arg1');

         if ($arg1) {
             $io->note(sprintf('You passed an argument: %s', $arg1));
         }

         if ($input->getOption('option1')) {
             // ...
         }*/

        $io = new SymfonyStyle($input, $output);
        $client = $this->fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/schedule');
        $stages = $client->data->stages;


        foreach ($stages as $stageAPI) {
            foreach ($stageAPI->weeks as $weekAPI) {
                $verif = $this->em->getRepository(Stages::class)->findOneBy(['name' => $stageAPI->name . " - " . $weekAPI->name]);
                $stage = new Stages();
                if (!is_null($verif)) {
                    $stage = $verif;
                }
                $stage->setName($stageAPI->name . " - " . $weekAPI->name);
                $stage->setStartDate(new \DateTime(date('Y-m-d H:i:s', $weekAPI->startDate / 1000)));
                $stage->setEndDate(new \DateTime(date('Y-m-d H:i:s', $weekAPI->endDate / 1000)));
                $stage->setWeek($stage->getStartDate()->format("W"));
                $io->writeln($stageAPI->name . " - " . $weekAPI->name);

                foreach ($weekAPI->matches as $matcheAPI) {
                    if (isset($matcheAPI->id)) {
                        $matcheAPI = $this->fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/match/' . $matcheAPI->id);

                        $verif = $this->em->getRepository(Stages::class)->findOneBy(['id' => $matcheAPI->id]);
                        $matche = new Matches();
                        if (!is_null($verif)) {
                            $matche = $verif;
                        }
                        $matche->setStages($stage);
                        $matche->setIdOWL($matcheAPI->id);
                        (isset($matcheAPI->handle)) ? $matche->setName($matcheAPI->handle) : false;
                        (isset($matcheAPI->startDate)) ? $matche->setStartDate(new \DateTime(date('Y-m-d H:i:s', $matcheAPI->startDate / 1000))) : false;
                        (isset($matcheAPI->endDate)) ? $matche->setEndDate(new \DateTime(date('Y-m-d H:i:s', $matcheAPI->endDate / 1000))) : false;
                        $scores = [
                            "A" => $matcheAPI->scores[0]->value,
                            "B" => $matcheAPI->scores[1]->value,
                        ];
                        $matche->setScores($scores);
                        if (isset($matcheAPI->competitors[0]->id) AND isset($matcheAPI->competitors[1]->id)) {
                            /** @var Team $teamA */
                            $teamA = $this->em->getRepository(Team::class)->findOneBy(['idOWL' => $matcheAPI->competitors[0]->id]);
                            if (!is_null($teamA)) {
                                $matche->setTeamA($teamA);
                            }
                            /** @var Team $teamB */
                            $teamB = $this->em->getRepository(Team::class)->findOneBy(['idOWL' => $matcheAPI->competitors[1]->id]);
                            if (!is_null($teamB)) {
                                $matche->setTeamB($teamB);
                            }
                            $io->writeln($teamA->getAbbreviatedName() . " - " . $teamB->getAbbreviatedName() . " - " . $matche->getIdOWL());
                        }
                        foreach ($matcheAPI->games as $gameAPI) {


                            $verif = $this->em->getRepository(Game::class)->findOneBy(['idOWL' => $gameAPI->id]);
                            $game = new Game();
                            if (!is_null($verif)) {
                                $game = $verif;
                            }

                            $game->setName($gameAPI->attributes->map);
                            $game->setIdOWL($gameAPI->id);
                            $game->setMatches($matche);

                            if (isset($gameAPI->points)) {
                                $game->setScoreTeamA($gameAPI->points[0]);
                                $game->setScoreTeamB($gameAPI->points[1]);
                            }
                            foreach ($gameAPI->players as $playerAPI) {
                                /** @var Player $verif */
                                $verif = $this->em->getRepository(Player::class)->findOneBy(['idOWL' => $playerAPI->player->id]);
                                $player = new Player();
                                if (!is_null($verif)) {
                                    $player = $verif;
                                    $gameplayed = new GamePlayed();
                                    $gameplayed->setPlayer($player);
                                    $gameplayed->setGame($game);
                                    $gameplayed->setTeam($player->getTeam()->getAbbreviatedName());

                                    $player->addGamePlayed($gameplayed);
                                    $game->addGamePlayed($gameplayed);
                                    $this->em->persist($gameplayed);
                                    $this->em->flush();
                                }

                            }

                            $this->em->persist($game);
                            $this->em->flush();
                        }
                        $this->em->persist($matche);
                        $this->em->flush();
                    }

                }

                $this->em->persist($stage);
                $this->em->flush();
            }


        }


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
