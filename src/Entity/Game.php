<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    const STATE = [
        "CONCLUDED",
        "PENDING"
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idOWL;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matches", inversedBy="games")
     */
    private $matches;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Player", inversedBy="games")
     */
    private $players;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreTeamA;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreTeamB;



    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->gamePlayeds = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIdOWL(): ?int
    {
        return $this->idOWL;
    }

    public function setIdOWL(?int $idOWL): self
    {
        $this->idOWL = $idOWL;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getMatches(): ?Matches
    {
        return $this->matches;
    }

    public function setMatches(?Matches $matches): self
    {
        $this->matches = $matches;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
        }

        return $this;
    }

    public function getScoreTeamA(): ?int
    {
        return $this->scoreTeamA;
    }

    public function setScoreTeamA(?int $scoreTeamA): self
    {
        $this->scoreTeamA = $scoreTeamA;

        return $this;
    }

    public function getScoreTeamB(): ?int
    {
        return $this->scoreTeamB;
    }

    public function setScoreTeamB(?int $scoreTeamB): self
    {
        $this->scoreTeamB = $scoreTeamB;

        return $this;
    }



}
