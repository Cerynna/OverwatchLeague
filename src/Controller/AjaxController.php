<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Roster;
use App\Entity\Stages;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/addRoster", name="addRsoter")
     * @param Request $request
     * @return JsonResponse
     */
    public function addRoster(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $userId = $request->get('user');
            $playerId = $request->get('player');

            /** @var User $user */
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $userId]);
            /** @var Player $player */
            $player = $this->getDoctrine()->getRepository(Player::class)->findOneBy(['idOWL' => $playerId]);

            $curentStage = $this->getDoctrine()->getRepository(Stages::class)->currentStage();


            if (is_null($user->getRosters()->toArray())) {
                $roster = new Roster();
                $roster->setStage($curentStage);
                $roster->addUser($user);
                $roster->addPlayer($player);
                $roster->setLastUpdate(new \DateTime('now'));
                $user->addRoster($roster);
                $this->getDoctrine()->getManager()->persist($roster);

            } else {
                $rosters = $user->getRosters()->toArray();
                /** @var Roster $roster */
                $roster = array_pop($rosters);
                $players = $roster->getPlayer()->toArray();

                if (count($players) <= 5) {
                    $arrPlayers = [];
                    /** @var Player $playerDB */
                    foreach ($players as $playerDB) {
                        $arrPlayers[] = $playerDB->getIdOWL();
                    }
                    if (!in_array($playerId, $arrPlayers)) {
                        $user->setMoney($user->getMoney() - $player->getPrize());
                        $roster->addPlayer($player);
                    } else {
                        return new JsonResponse(["error", "Vous Avez déja " . $player->getName() . " dans votre Roster"]);
                    }

                    $roster->setLastUpdate(new \DateTime('now'));


                } else {
                    return new JsonResponse(["error", "Vous ne pouvez pas avoir plus de 6 joueur dans votre Roster"]);
                }

            }

            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(["valid", $player->getName() . " a été ajouté dans votre Roster"]);
        } else {
            return new JsonResponse("BAD REQUEST", "400");
        }
    }
}
