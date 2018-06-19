<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GamePlayedRepository")
 */
class GamePlayed
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="gamePlayeds")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="gamePlayeds", cascade={"persist"}))
     */
    private $game;

    public function getId()
    {
        return $this->id;
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function setGame($game)
    {
        $this->game = $game;

        return $this;
    }
}
