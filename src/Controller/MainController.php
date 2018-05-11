<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Service\UpdateFromAPI;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{


    /**
     * @Route("/teams", name="teams")
     */
    public function teams(UpdateFromAPI $fromAPI)
    {
       /* $verif = $this->getDoctrine()->getRepository(Team::class)->findOneBy(['idOWL' => 12]);*/

        /*dump($verif);*/

        $verif = $fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/matches/10525');
        dump($verif);
        /*
        $verif = $fromAPI->CallAPI('GET', 'https://api.overwatchleague.com/matches/10548');
        dump($verif);*/
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
    public function team($idTeam){
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
    public function player($idPlayer){
        /** @var Player $player */
       $player = $this->getDoctrine()->getRepository(Player::class)->findOneBy(['idOWL' => $idPlayer]);
         dump($player->getGamePlayeds()->toArray());

        return $this->render('main/player.html.twig', [
            'controller_name' => 'MainController',
            'player' => $player,
        ]);
    }

}
