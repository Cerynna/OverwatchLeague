<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Stages;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Repository\StagesRepository;
use App\Service\ToolsPlayers;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppUpdateScoreCommand extends Command
{
    protected static $defaultName = 'app:update:score';


    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;


    /**
     * @var ToolsPlayers
     */
    private $toolsPlayers;

    public function __construct($name = null, EntityManagerInterface $entityManager, ToolsPlayers $toolsPlayers)
    {
        $this->em = $entityManager;
        $this->toolsPlayers = $toolsPlayers;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addOption('dry', null, InputOption::VALUE_NONE, 'Dry Update')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Update Player')
            ->addOption('archive', null, InputOption::VALUE_NONE, 'Archivage Stat')
            ->addOption('reset', null, InputOption::VALUE_NONE, 'Reset Player Stat');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $outputStyle = new OutputFormatterStyle('red', null, array('bold', 'blink'));
        $output->getFormatter()->setStyle('fire', $outputStyle);

        /** @var PlayerRepository $playerRepo */
        $playerRepo = $this->em->getRepository(Player::class);
        /** @var StagesRepository $stageRepo */
        $stageRepo = $this->em->getRepository(Stages::class);
        /** @var GameRepository $gameRepo */
        $gameRepo = $this->em->getRepository(Game::class);

        $players = $playerRepo->allOrder('ASC', 'team');
        $option = "";

        if ($input->getOption('archive')) {

            $stages = $stageRepo->findBy([], ['startDate' => 'ASC']);
            $choices = [];
            /** @var Stages $stage */
            foreach ($stages as $stage) {
                if (empty($stage->getArchiveStats()->toArray())) {
                    if (new DateTime("today") > $stage->getStartDate() AND !is_null($stage->getStartDate()))
                        $choices[$stage->getId()] = $stage->getName() . " | " . $stage->getStartDate()->format("d-m-Y");
                }
            }
            $invertChoices = array_flip($choices);
            $defaultChoise = array_shift($invertChoices);
            $invertChoices = array_flip($choices);

            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion('Choice one Week', $choices, $defaultChoise);
            $question->setErrorMessage('Color %s is invalid.');

            $stageName = $helper->ask($input, $output, $question);


            $output->writeln('You have just selected: ' . $invertChoices[$stageName]);

            $stage = $stageRepo->findOneBy(['id' => $invertChoices[$stageName]]);

            $arrMatch = $stage->getMatches()->toArray();

            $gameRepo->playerByMatch($arrMatch);

            $this->toolsPlayers->archiveStats($stage);

            $option = "Mode : Archivage";

        }
        if ($input->getOption('update')) {
            $option = "Mode : Update";
            $io->note(sprintf('Update Player'));

            foreach ($players as $player) {
                $newStat = $this->toolsPlayers->getStatPlayer($player);
                if (!is_null($newStat) and !empty($newStat)) {
                    $player->setStats($newStat);
                    $stats = [];
                    foreach ($player->getStats() as $key => $value) {
                        $stats[$key] = $value;
                    }
                    $player->setPrize($stats['prize']);
                    $player->setBonu($stats['bonus']);
                    $player->setGain($stats['gain']);
                    $player->setRatio($stats['ratioPrize']);

                }
            }
            $this->em->flush();
        }
        $io->success('Update : ' . count($players) . " Players " . $option);
    }


    public function addSpace($string, $max, $type, $separator = null, $comparator = null)
    {

        $spaceRight = 0;
        $spaceLeft = 0;
        $countString = strlen($string);
        $space = $max - $countString;
        switch ($type) {
            case "center":
                if ($space % 2 == 1) {
                    $spaceLeft = floor($space / 2);
                    $spaceRight = ceil($space / 2);
                } else {
                    $spaceLeft = $space / 2;
                    $spaceRight = $space / 2;
                }
                break;
            case "left":
                $spaceLeft = 1;
                $spaceRight = $space;
                break;
            case "right":
                $spaceLeft = $space;
                $spaceRight = 1;
                break;
        }

        if (!is_null($comparator)) {
            if ($string === $comparator) {
                $string = "$string";
            }
            if ($string < $comparator) {
                $string = "<fire>$string</fire>";
            }
            if ($string > $comparator) {
                $string = "<info>$string</info>";
            }
        }

        for ($r = 0; $r <= $spaceRight; $r++) {
            $string = $string . " ";
        }
        for ($l = 0; $l <= $spaceLeft; $l++) {
            $string = " " . $string;
        }

        if (!is_null($separator)) {
            $string = $string . $separator;
        }

        return $string;

    }

}
