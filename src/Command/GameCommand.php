<?php

namespace App\Command;

use App\Entity\Stages;
use App\Repository\StagesRepository;
use App\Service\ToolsPlayers;
use App\Service\UpdateFromAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class GameCommand extends Command
{
    protected static $defaultName = 'app:updateDB';

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
            ->addOption('matchs', null, InputOption::VALUE_NONE, 'Update Matchs')
            ->addOption('stages', null, InputOption::VALUE_NONE, 'Update Stages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        /*$client = $this->fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/schedule');
        $stages = $client->data->stages;*/
        /** @var StagesRepository $stageRepo */
        $stageRepo = $this->em->getRepository(Stages::class);
        if ($input->getOption('stages')) {

            $stages = $stageRepo->findBy([], ['startDate' => 'ASC']);
            $choices = [];
            /** @var Stages $stage */
            foreach ($stages as $stage) {
                if (empty($stage->getArchiveStats()->toArray())) {
                    if (new \DateTime("today") > $stage->getStartDate() AND !is_null($stage->getStartDate()))
                        $choices[$stage->getId()] = $stage->getName() . " | " . $stage->getStartDate()->format("d-m-Y");
                }
            }
            $invertChoices = array_flip($choices);
            $defaultChoise = array_shift($invertChoices);
            $invertChoices = array_flip($choices);

            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion('Choice one Stages', $choices, $defaultChoise);
            $question->setErrorMessage('Color %s is invalid.');

            $stageName = $helper->ask($input, $output, $question);


            $output->writeln('You have just selected: ' . $invertChoices[$stageName]);

            $stage = $stageRepo->findOneBy(['id' => $invertChoices[$stageName]]);

            $arrMatch = $stage->getMatches()->toArray();

            $this->fromAPI->GetMatch($arrMatch, $output);



        }
        if ($input->getOption('matchs')) {

        }

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
