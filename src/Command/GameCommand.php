<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\GamePlayed;
use App\Entity\Matches;
use App\Entity\Player;
use App\Entity\Team;
use App\Service\UpdateFromAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GameCommand extends Command
{
    protected static $defaultName = 'app:game';

    /**
     * @var UpdateFromAPI
     */
    private $fromAPI;

    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct($name = null, UpdateFromAPI $fromAPI, EntityManagerInterface $entityManager)
    {
        $this->fromAPI = $fromAPI;
        $this->em = $entityManager;
        parent::__construct($name);
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');*/

        $client = $this->fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/schedule');

        $stages = $client->data->stages;

        $message = "";
        $i = 1;
        foreach ($stages as $stage) {

            foreach ($stage->matches as $matchAPI) {

                $verif = $this->em->getRepository(Matches::class)->findOneBy(['idOWL' => $matchAPI->id]);

                $type = "<info>CREATE</info>";
                $match = new Matches();

                if (!is_null($verif)) {
                    $match = $verif;
                    $type = "<comment>UPDATE</comment>";
                }

                $match->setIdOWL($matchAPI->id);

                $matchAPI = $this->fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/matches/' . $matchAPI->id);

                if (isset($matchAPI->competitors) and !is_null($matchAPI->competitors)) {
                    $teamA = $matchAPI->competitors[0];
                    $teamB = $matchAPI->competitors[1];


                    /** @var Team $teamADB */
                    $teamADB = $this->em->getRepository(Team::class)->findOneBy(['idOWL' => $teamA->id]);
                    /** @var Team $teamBDB */
                    $teamBDB = $this->em->getRepository(Team::class)->findOneBy(['idOWL' => $teamB->id]);
                    $match->setTeamA($teamADB);
                    $match->setTeamB($teamBDB);


                    $output->writeln($i . "   -   " . $matchAPI->id . "  -   " . $matchAPI->scores[0]->value . " - " . $matchAPI->scores[1]->value . "   " . $type);


                    if (!empty($matchAPI)) {

                        foreach ($matchAPI->games as $gameAPI) {
                            $verif = $this->em->getRepository(Game::class)->findOneBy(['idOWL' => $gameAPI->id]);
                            $type = "<info>CREATE</info>";
                            $game = new Game();
                            if (!is_null($verif)) {
                                $game = $verif;
                                $type = "<comment>UPDATE</comment>";
                            }
                            $game->setIdOWL($gameAPI->id);

                            if (isset($gameAPI->attributes->map) and !is_null($gameAPI->attributes->map)) {
                                $game->setName($gameAPI->attributes->map);
                            }
                            if (isset($gameAPI->attributes->mapScore) and !is_null($gameAPI->attributes->mapScore)) {
                                $game->setScoreTeamA($gameAPI->attributes->mapScore->team1);
                                $game->setScoreTeamB($gameAPI->attributes->mapScore->team2);
                            }

                            if (isset($teamA->abbreviatedName) and !is_null($teamA->abbreviatedName) AND isset($teamB->abbreviatedName) and !is_null($teamB->abbreviatedName)) {
                                $output->writeln($gameAPI->id . "  -   " . $teamA->abbreviatedName . " " . $game->getScoreTeamA() . " - " . $game->getScoreTeamB() . " " . $teamB->abbreviatedName . "  |   " . $gameAPI->attributes->map);


                            }
                            $i = $i + 1;


                            /**
                             * start match
                             * end match
                             *
                             * scoreTeamA game
                             * scoreTeamB game
                             * name = map
                             *
                             *
                             *
                             *
                             */

                            foreach ($gameAPI->players as $playerAPI) {
                                /** @var Player $player */
                                $player = $this->em->getRepository(Player::class)->findOneBy(['idOWL' => $playerAPI->player->id]);
                                if (!is_null($player)) {
                                    $gamePlayed = new GamePlayed();
                                    $gamePlayed->addPlayer($player);
                                    $gamePlayed->addGame($game);
                                    $this->em->persist($gamePlayed);
                                    $this->em->flush();
                                }/* else {
                                $output->writeln($playerAPI->player->name);
                            }*/

                            }

                            $date = new \DateTime();

                            $match->setStartDate(new \DateTime($date->setTimestamp($matchAPI->startDate)->format('Y-m-d H:i:s e')));
                            $match->setEndDate(new \DateTime($date->setTimestamp($matchAPI->endDate)->format('Y-m-d H:i:s e')));

                            $match->addGame($game);
                            $this->em->persist($game);

                        }
                    }
                }
                $this->em->persist($match);
                $this->em->flush();
            }
        }
        /*$this->em->flush();*/
    }


}
