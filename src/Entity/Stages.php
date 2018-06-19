<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StagesRepository")
 */
class Stages
{
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
     * @ORM\OneToMany(targetEntity="App\Entity\Matches", mappedBy="stages")
     */
    private $matches;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $week;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArchiveStats", mappedBy="stage")
     */
    private $archiveStats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Roster", mappedBy="stage")
     */
    private $rosters;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->archiveStats = new ArrayCollection();
        $this->rosters = new ArrayCollection();
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

    /**
     * @return Collection|Matches[]
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    public function addMatch(Matches $match): self
    {
        if (!$this->matches->contains($match)) {
            $this->matches[] = $match;
            $match->setStages($this);
        }

        return $this;
    }

    public function removeMatch(Matches $match): self
    {
        if ($this->matches->contains($match)) {
            $this->matches->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getStages() === $this) {
                $match->setStages(null);
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

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(?int $week): self
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @return Collection|ArchiveStats[]
     */
    public function getArchiveStats(): Collection
    {
        return $this->archiveStats;
    }

    public function addArchiveStat(ArchiveStats $archiveStat): self
    {
        if (!$this->archiveStats->contains($archiveStat)) {
            $this->archiveStats[] = $archiveStat;
            $archiveStat->setStage($this);
        }

        return $this;
    }

    public function removeArchiveStat(ArchiveStats $archiveStat): self
    {
        if ($this->archiveStats->contains($archiveStat)) {
            $this->archiveStats->removeElement($archiveStat);
            // set the owning side to null (unless already changed)
            if ($archiveStat->getStage() === $this) {
                $archiveStat->setStage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Roster[]
     */
    public function getRosters(): Collection
    {
        return $this->rosters;
    }

    public function addRoster(Roster $roster): self
    {
        if (!$this->rosters->contains($roster)) {
            $this->rosters[] = $roster;
            $roster->setStage($this);
        }

        return $this;
    }

    public function removeRoster(Roster $roster): self
    {
        if ($this->rosters->contains($roster)) {
            $this->rosters->removeElement($roster);
            // set the owning side to null (unless already changed)
            if ($roster->getStage() === $this) {
                $roster->setStage(null);
            }
        }

        return $this;
    }
}
