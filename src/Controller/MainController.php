<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\Player;
use App\Entity\Roster;
use App\Entity\Stages;
use App\Entity\Team;
use App\Entity\User;
use App\Service\ToolsPlayers;
use App\Service\UpdateFromAPI;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends Controller
{

    /**
     * @Route("/", name="teams")
     */
    public function teams(UpdateFromAPI $fromAPI)
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'teams' => $teams,
        ]);
    }

    /**
     * @Route("/team/{idTeam}", name="team")
     * @param integer $idTeam
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function team($idTeam)
    {
        $team = $this->getDoctrine()->getRepository(Team::class)->findOneBy(['idOWL' => $idTeam]);
        /* dump($team);*/

        return $this->render('main/team.html.twig', [
            'controller_name' => 'MainController',
            'team' => $team,
        ]);
    }

    /**
     * @Route("/player/{idPlayer}", name="player")
     * @param integer $idPlayer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function player($idPlayer)
    {
        /** @var Player $player */
        $player = $this->getDoctrine()->getRepository(Player::class)->findOneBy(['idOWL' => $idPlayer]);
        $statsPlayer = $player->getArchiveStats()->toArray();
        return $this->render('main/player.html.twig', [
            'controller_name' => 'MainController',
            'player' => $player,
            'statsPlayer' => $statsPlayer,
        ]);
    }

    /**
     * @Route("/players", name="players")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function players()
    {
        /** @var Player $player */
        $playerRepo = $this->getDoctrine()->getRepository(Player::class);
        $players = $playerRepo->allOrder('DESC', 'ratio', 20);
        /*dump($players);*/
        /* dump($player->getGamePlayeds()->toArray()[0]);*/
        return $this->render('main/players.html.twig', [
            'controller_name' => 'MainController',
            'players' => $players,
        ]);
    }

    /**
     * @Route("/matchs", name="matchs")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchs()
    {
        /** @var Player $player */
        $matchRepo = $this->getDoctrine()->getRepository(Matches::class);
        $matches = $matchRepo->findAll();


        return $this->render('main/matchs.html.twig', [
            'controller_name' => 'MainController',
            'matches' => $matches,
        ]);
    }

    /**
     * @Route("/user", name="user")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function user(UserPasswordEncoderInterface $encoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Player $player */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->findOneBy(['ip' => $_SERVER['REMOTE_ADDR']]);

        if (is_null($user)) {
            dump("NEW USER");
            $user = new User();
            $user->setIp($_SERVER['REMOTE_ADDR']);
            $user->setUserName("cerynna");
            $user->setStatus(User::STATUS['admin']);


            // whatever *your* User object is
            $plainPassword = 'azerty';
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);

            $entityManager->persist($user);
            $entityManager->flush();
        }
        $this->getUser();

        dump($this->getUser());

        return $this->render('main/clean.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/roster", name="roster")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function roster()
    {
        /** @var User $user */
        $user = $this->getUser();
        $rosters = $user->getRosters()->toArray();
        /** @var Roster $roster */
        $roster = array_shift($rosters);

        /** @var Stages $currentStage */
        $currentStage = $this->getDoctrine()->getRepository(Stages::class)->currentStage();


        return $this->render('main/myRoster.html.twig', [
            'controller_name' => 'MainController',
            'roster' => $roster,
            "currentStage" => $currentStage
        ]);
    }

    /**
     * @Route("/reward", name="reward")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rewrad(ToolsPlayers $toolsPlayers)
    {
        /** @var User $user */
        $user = $this->getUser();
        $rosters = $user->getRosters()->toArray();
        /** @var Roster $roster */
        $roster = array_shift($rosters);


        $toolsPlayers->getRewardRoster($roster);


        return $this->render('main/reward.html.twig', [
            'controller_name' => 'MainController',

        ]);
    }

}
