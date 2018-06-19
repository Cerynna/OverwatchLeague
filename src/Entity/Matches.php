<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchesRepository")
 */
class Matches
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idOWL;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="matches")
     */
    private $teamA;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="matches")
     */
    private $TeamB;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="matches")
     */
    private $games;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $scores;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stages", inversedBy="matches", cascade={"persist"})
     */
    private $stages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function getTeamA(): ?Team
    {
        return $this->teamA;
    }

    public function setTeamA(?Team $teamA): self
    {
        $this->teamA = $teamA;

        return $this;
    }

    public function getTeamB(): ?Team
    {
        return $this->TeamB;
    }

    public function setTeamB(?Team $TeamB): self
    {
        $this->TeamB = $TeamB;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setMatches($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getMatches() === $this) {
                $game->setMatches(null);
            }
        }

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function __toString()
    {

        return "Ca bug";
    }

    public function getScores(): ?array
    {
        return $this->scores;
    }

    public function setScores(?array $scores): self
    {
        $this->scores = $scores;

        return $this;
    }

    public function getStages(): ?Stages
    {
        return $this->stages;
    }

    public function setStages(?Stages $stages): self
    {
        $this->stages = $stages;

        return $this;
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
}
