<?php

namespace App\Command;

use App\Entity\Player;
use App\Entity\Team;
use App\Service\UpdateFromAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TeamAndPlayerCommand extends Command
{

    protected static $defaultName = 'app:team';

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
            ->setDescription('Add DB for Player and Team')
            ->setHelp('This command add to DB Player and TEam from API OverWatchLeague')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $client = $this->fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/teams');

        foreach ($client->competitors as $teamAPI) {


            /** @var Team $verif */
            $verif = $this->em->getRepository(Team::class)->findOneBy(['idOWL' => $teamAPI->competitor->id]);

            $type = "<info>CREATE</info>";
            $team = new Team();
            $message = "";
            if (!is_null($verif)) {
                $team = $verif;
                $type = "<comment>UPDATE</comment>";
            }
            $team->setName($teamAPI->competitor->name);
            $team->setIdOWL($teamAPI->competitor->id);
            $team->setHandle($teamAPI->competitor->handle);
            $team->setHomeLocation($teamAPI->competitor->homeLocation);
            $team->setPrimaryColor($teamAPI->competitor->primaryColor);
            $team->setSecondaryColor($teamAPI->competitor->secondaryColor);
            $team->setAbbreviatedName($teamAPI->competitor->abbreviatedName);
            $team->setLogo($teamAPI->competitor->icon);

            foreach ($teamAPI->competitor->accounts as $accounts) {
                switch ($accounts->accountType) {
                    case "TWITTER":
                        $team->setTwitter($accounts->value);
                        break;
                    case "DISCORD":
                        $team->setDiscord($accounts->value);
                        break;
                    case "INSTAGRAM":
                        $team->setInstagram($accounts->value);
                        break;
                    case "FACEBOOK":
                        $team->setFacebook($accounts->value);
                        break;
                    case "YOUTUBE_CHANNEL":
                        $team->setYoutube($accounts->value);
                        break;
                    case "TWITCH":
                        $team->setTwitch($accounts->value);
                        break;
                }
            }
            foreach ($teamAPI->competitor->players as $playerAPI) {
                /** @var Team $verif */
                $verif = $this->em->getRepository(Player::class)->findOneBy(['idOWL' => $playerAPI->player->id]);

                $typePlayer = "<info>CREATE</info>";
                $player = new Player();
                if (!is_null($verif)) {
                    $player = $verif;
                    $typePlayer = "<comment>UPDATE</comment>";
                }

                (isset($playerAPI->player->id)) ? $player->setIdOWL($playerAPI->player->id) : false;
                (isset($playerAPI->player->name)) ? $player->setName($playerAPI->player->name) : false;
                (isset($playerAPI->player->givenName)) ? $player->setFirstName($playerAPI->player->givenName) : false;
                (isset($playerAPI->player->familyName)) ? $player->setLastName($playerAPI->player->familyName) : false;
                (isset($playerAPI->player->handle)) ? $player->setHandle($playerAPI->player->handle) : false;
                (isset($playerAPI->player->homeLocation)) ? $player->setHomeLocation($playerAPI->player->homeLocation) : false;
                (isset($playerAPI->player->nationality)) ? $player->setNationality($playerAPI->player->nationality) : false;
                (isset($playerAPI->player->headshot)) ? $player->setHeadshot($playerAPI->player->headshot) : false;

                (isset($playerAPI->player->attributes->heroes)) ? $player->setHeroes($playerAPI->player->attributes->heroes) : false;
                (isset($playerAPI->player->attributes->player_number)) ? $player->setPlayerNumber($playerAPI->player->attributes->player_number) : false;
                (isset($playerAPI->player->attributes->role)) ? $player->setRole($playerAPI->player->attributes->role) : false;

                foreach ($playerAPI->player->accounts as $accounts) {

                    switch ($accounts->accountType) {
                        case "TWITTER":
                            $player->setTwitter($accounts->value);
                            break;
                        case "DISCORD":
                            $player->setDiscord($accounts->value);
                            break;
                        case "INSTAGRAM":
                            $player->setInstagram($accounts->value);
                            break;
                        case "FACEBOOK":
                            $player->setFacebook($accounts->value);
                            break;
                        case "YOUTUBE_CHANNEL":
                            $player->setYoutube($accounts->value);
                            break;
                        case "TWITCH":
                            $player->setTwitch($accounts->value);
                            break;
                    }

                }
                $message = $message . $player->getName() . " - " . $typePlayer . "\n";
                $this->em->persist($player);
                $team->addPlayer($player);

            }
            $this->em->persist($team);
            $output->writeln('=====================================');
            $output->writeln("      <fg=black;bg=green>" . $team->getName() . "</> - " . $type);
            $output->writeln('=====================================');
            $output->writeln($message);

        }
        $this->em->flush();


    }

}