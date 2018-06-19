<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
{


    const   PRIZE = 150;
    const   BONUS_PRIZE = [
        "map" => 25
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $handle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $homeLocation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $youtube;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $heroes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $player_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $headshot;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discord;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instagram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idOWL;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="players")
     */
    private $team;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationality;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GamePlayed", mappedBy="player")
     */
    private $gamePlayeds;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $stats;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prize;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bonus;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gain;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArchiveStats", mappedBy="player")
     */
    private $archiveStats;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ratio;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Roster", mappedBy="player")
     */
    private $rosters;




    public function __construct()
    {
        $this->gamePlayeds = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }

    public function setHandle(?string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function getHomeLocation(): ?string
    {
        return $this->homeLocation;
    }

    public function setHomeLocation(?string $homeLocation): self
    {
        $this->homeLocation = $homeLocation;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    public function setTwitch(?string $twitch): self
    {
        $this->twitch = $twitch;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getHeroes(): ?array
    {
        return $this->heroes;
    }

    public function setHeroes(?array $heroes): self
    {
        $this->heroes = $heroes;

        return $this;
    }

    public function getPlayerNumber(): ?int
    {
        return $this->player_number;
    }

    public function setPlayerNumber(?int $player_number): self
    {
        $this->player_number = $player_number;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getHeadshot(): ?string
    {
        return $this->headshot;
    }

    public function setHeadshot(?string $headshot): self
    {
        $this->headshot = $headshot;

        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

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

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection|GamePlayed[]
     */
    public function getGamePlayeds(): Collection
    {
        return $this->gamePlayeds;
    }

    public function addGamePlayed(GamePlayed $gamePlayed): self
    {
        if (!$this->gamePlayeds->contains($gamePlayed)) {
            $this->gamePlayeds[] = $gamePlayed;
            $gamePlayed->setPlayer($this);
        }

        return $this;
    }

    public function removeGamePlayed(GamePlayed $gamePlayed): self
    {
        if ($this->gamePlayeds->contains($gamePlayed)) {
            $this->gamePlayeds->removeElement($gamePlayed);
            // set the owning side to null (unless already changed)
            if ($gamePlayed->getPlayer() === $this) {
                $gamePlayed->setPlayer(null);
            }
        }

        return $this;
    }

    public function getStats(): ?array
    {
        return $this->stats;
    }

    public function setStats(?array $stats): self
    {
        $this->stats = $stats;

        return $this;
    }

    public function getPrize(): ?int
    {
        return $this->prize;
    }

    public function setPrize(?int $prize): self
    {
        $this->prize = $prize;

        return $this;
    }

    public function getBonus(): ?int
    {
        return $this->bonus;
    }

    public function setBonu(?int $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getGain(): ?int
    {
        return $this->gain;
    }

    public function setGain(?int $gain): self
    {
        $this->gain = $gain;

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
            $archiveStat->setPlayer($this);
        }

        return $this;
    }

    public function removeArchiveStat(ArchiveStats $archiveStat): self
    {
        if ($this->archiveStats->contains($archiveStat)) {
            $this->archiveStats->removeElement($archiveStat);
            // set the owning side to null (unless already changed)
            if ($archiveStat->getPlayer() === $this) {
                $archiveStat->setPlayer(null);
            }
        }

        return $this;
    }

    public function getRatio(): ?int
    {
        return $this->ratio;
    }

    public function setRatio(?int $ratio): self
    {
        $this->ratio = $ratio;

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
            $roster->addPlayer($this);
        }

        return $this;
    }

    public function removeRoster(Roster $roster): self
    {
        if ($this->rosters->contains($roster)) {
            $this->rosters->removeElement($roster);
            $roster->removePlayer($this);
        }

        return $this;
    }

}
