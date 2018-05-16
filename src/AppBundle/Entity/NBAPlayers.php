<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NBAPlayers
 *
 * @ORM\Table(name="n_b_a_players")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NBAPlayersRepository")
 */
class NBAPlayers
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var int
     *
     * @ORM\Column(name="player_id", type="integer", unique=true)
     */
    public $playerId;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    public $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    public $lastname;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    public $rating;

    /**
     * @var integer
     *
     * @ORM\Column(name="jersey", type="integer", nullable=true)
     */
    public $jersey;

    /**
     * @var string
     * @ORM\Column(name="position", type="string", nullable=true)
     */
    public $position;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", nullable=true)
     */
    public $height;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", nullable=true)
     */
    public $weight;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbaDebutYear", type="integer", nullable=true)
     */
    public $nbaDebutYear;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    public $country;
    /**
     * @ORM\ManyToOne(targetEntity="NBATeams", inversedBy="NBAPlayers")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    public $teamId;
    /**
     * @var integer
     *
     * @ORM\Column(name="offensiveRating", type="integer", nullable=true)
     */
    public $offensiveRating;

    /**
     * @var integer
     *
     * @ORM\Column(name="defensiveRating", type="integer", nullable=true)
     */
    public $defensiveRating;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set playerId
     *
     * @param integer $playerId
     *
     * @return NBAPlayers
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId
     *
     * @return int
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }
    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
    public function getFullName(){
        return $this->firstname.' '.$this->lastname;
    }
    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }
    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
    /**
     * @return int
     */
    public function getJersey()
    {
        return $this->jersey;
    }

    /**
     * @param int $jersey
     */
    public function setJersey($jersey)
    {
        $this->jersey = $jersey;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getNbaDebutYear()
    {
        return $this->nbaDebutYear;
    }

    /**
     * @param int $nbaDebutYear
     */
    public function setNbaDebutYear($nbaDebutYear)
    {
        $this->nbaDebutYear = $nbaDebutYear;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }
    /**
     * @return int
     */
    public function getOffensiveRating()
    {
        return $this->offensiveRating;
    }

    /**
     * @param int $offensiveRating
     */
    public function setOffensiveRating($offensiveRating)
    {
        $this->offensiveRating = $offensiveRating;
    }

    /**
     * @return int
     */
    public function getDefensiveRating()
    {
        return $this->defensiveRating;
    }

    /**
     * @param int $defensiveRating
     */
    public function setDefensiveRating($defensiveRating)
    {
        $this->defensiveRating = $defensiveRating;
    }

    public function getRarity()
    {
        if($this->getRating() == 0) {
            return 'RK';
        } elseif ($this->getRating() > 95) {
            return 'E';
        } elseif ($this->getRating() > 90) {
            return 'UR';
        } elseif ($this->getRating() > 87) {
            return 'SR';
        } elseif ($this->getRating() > 80) {
            return 'R';
        }

        return 'N';
    }

    public function getNote()
    {
        if($this->getRating() == 0) {
            return 'RK';
        }

        return $this->getRating();
    }
}

