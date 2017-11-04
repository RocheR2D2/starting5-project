<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var int
     *
     * @ORM\Column(name="shirt_number", type="integer")
     */
    private $shirtNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="player")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="player")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * @var float
     *
     * @ORM\Column(name="FG_percentage", type="float")
     */
    private $fGPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="ThreePoints_percentage", type="float")
     */
    private $threePointsPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="FT_percentage", type="float")
     */
    private $fTPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="PPG", type="float")
     */
    private $pPG;

    /**
     * @var float
     *
     * @ORM\Column(name="RPG", type="float")
     */
    private $rPG;

    /**
     * @var float
     *
     * @ORM\Column(name="APG", type="float")
     */
    private $aPG;

    /**
     * @var float
     *
     * @ORM\Column(name="BPG", type="float")
     */
    private $bPG;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float")
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=255)
     */
    private $weight;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="born", type="date")
     */
    private $born;

    /**
     * @ORM\ManyToOne(targetEntity="State", inversedBy="player")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="nba_debut", type="integer")
     */
    private $nbaDebut;


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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Player
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Player
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set shirtNumber
     *
     * @param integer $shirtNumber
     *
     * @return Player
     */
    public function setShirtNumber($shirtNumber)
    {
        $this->shirtNumber = $shirtNumber;

        return $this;
    }

    /**
     * Get shirtNumber
     *
     * @return int
     */
    public function getShirtNumber()
    {
        return $this->shirtNumber;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Player
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set team
     *
     * @param integer $team
     *
     * @return Player
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return int
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set fGPercentage
     *
     * @param float $fGPercentage
     *
     * @return Player
     */
    public function setFGPercentage($fGPercentage)
    {
        $this->fGPercentage = $fGPercentage;

        return $this;
    }

    /**
     * Get fGPercentage
     *
     * @return float
     */
    public function getFGPercentage()
    {
        return $this->fGPercentage;
    }

    /**
     * Set tThreePointsPercentage
     *
     * @param float $threePointsPercentage
     *
     * @return Player
     */
    public function setThreePointsPercentage($threePointsPercentage)
    {
        $this->threePointsPercentage = $threePointsPercentage;

        return $this;
    }

    /**
     * Get ThreePointsPercentage
     *
     * @return float
     */
    public function getThreePointsPercentage()
    {
        return $this->threePointsPercentage;
    }

    /**
     * Set fTPercentage
     *
     * @param float $fTPercentage
     *
     * @return Player
     */
    public function setFTPercentage($fTPercentage)
    {
        $this->fTPercentage = $fTPercentage;

        return $this;
    }

    /**
     * Get fTPercentage
     *
     * @return float
     */
    public function getFTPercentage()
    {
        return $this->fTPercentage;
    }

    /**
     * Set pPG
     *
     * @param float $pPG
     *
     * @return Player
     */
    public function setPPG($pPG)
    {
        $this->pPG = $pPG;

        return $this;
    }

    /**
     * Get pPG
     *
     * @return float
     */
    public function getPPG()
    {
        return $this->pPG;
    }

    /**
     * Set rPG
     *
     * @param float $rPG
     *
     * @return Player
     */
    public function setRPG($rPG)
    {
        $this->rPG = $rPG;

        return $this;
    }

    /**
     * Get rPG
     *
     * @return float
     */
    public function getRPG()
    {
        return $this->rPG;
    }

    /**
     * Set aPG
     *
     * @param float $aPG
     *
     * @return Player
     */
    public function setAPG($aPG)
    {
        $this->aPG = $aPG;

        return $this;
    }

    /**
     * Get aPG
     *
     * @return float
     */
    public function getAPG()
    {
        return $this->aPG;
    }

    /**
     * Set bPG
     *
     * @param float $bPG
     *
     * @return Player
     */
    public function setBPG($bPG)
    {
        $this->bPG = $bPG;

        return $this;
    }

    /**
     * Get bPG
     *
     * @return float
     */
    public function getBPG()
    {
        return $this->bPG;
    }

    /**
     * Set height
     *
     * @param float $height
     *
     * @return Player
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return Player
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set born
     *
     * @param \DateTime $born
     *
     * @return Player
     */
    public function setBorn($born)
    {
        $this->born = $born;

        return $this;
    }

    /**
     * Get born
     *
     * @return \DateTime
     */
    public function getBorn()
    {
        return $this->born;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Player
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set nbaDebut
     *
     * @param integer $nbaDebut
     *
     * @return Player
     */
    public function setNbaDebut($nbaDebut)
    {
        $this->nbaDebut = $nbaDebut;

        return $this;
    }

    /**
     * Get nbaDebut
     *
     * @return int
     */
    public function getNbaDebut()
    {
        return $this->nbaDebut;
    }
}

