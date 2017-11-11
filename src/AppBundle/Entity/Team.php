<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug_team", type="string", length=255)
     */
    private $slugTeam;

    /**
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="team")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     */
    private $division;

    /**
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="team")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $conference;

    /**
     * @ORM\ManyToOne(targetEntity="State", inversedBy="team")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="Stadium", inversedBy="team")
     * @ORM\JoinColumn(name="stadium_id", referencedColumnName="id")
     */
    private $stadium;

    /**
     * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="team")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private $trainer;

    /**
     * @ORM\ManyToOne(targetEntity="Town", inversedBy="team")
     * @ORM\JoinColumn(name="town_id", referencedColumnName="id")
     */
    private $town;

    /**
     * One Team has Many Players.
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team", fetch="EAGER")
     */
    private $players;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_top", type="boolean")
     */
    private $isTop;

    const POINT_GUARD_POSITION_ID = 1;
    const SHOOTING_GUARD_POSITION_ID = 2;
    const SMALL_FORWARD_POSITION_ID = 3;
    const POWER_FORWARD_POSITION_ID = 4;
    const CENTER_POSITION_ID = 5;

    /**
     * Team constructor.
     */
    public function __construct() {
        $this->players = new ArrayCollection();
    }

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
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slugTeam
     *
     * @param string $slugTeam
     *
     * @return Team
     */
    public function setSlugTeam($slugTeam)
    {
        $this->slugTeam = $slugTeam;

        return $this;
    }

    /**
     * Get slugTeam
     *
     * @return string
     */
    public function getSlugTeam()
    {
        return $this->slugTeam;
    }

    /**
     * Set division
     *
     * @param integer $division
     *
     * @return Team
     */
    public function setDivision($division)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return int
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set conference
     *
     * @param integer $conference
     *
     * @return Team
     */
    public function setConference($conference)
    {
        $this->conference = $conference;

        return $this;
    }

    /**
     * Get conference
     *
     * @return int
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Team
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set stadium
     *
     * @param integer $stadium
     *
     * @return Team
     */
    public function setStadium($stadium)
    {
        $this->stadium = $stadium;

        return $this;
    }

    /**
     * Get stadium
     *
     * @return int
     */
    public function getStadium()
    {
        return $this->stadium;
    }

    /**
     * Set trainer
     *
     * @param integer $trainer
     *
     * @return Team
     */
    public function setTrainer($trainer)
    {
        $this->trainer = $trainer;

        return $this;
    }

    /**
     * Get trainer
     *
     * @return int
     */
    public function getTrainer()
    {
        return $this->trainer;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return Team
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }
    /**
     * @return bool
     */
    public function isTop()
    {
        return $this->isTop;
    }

    /**
     * @param bool $isTop
     */
    public function setIsTop($isTop)
    {
        $this->isTop = $isTop;
    }

    public function getPointGuards(){
        $pointGuards = new ArrayCollection();
        foreach($this->getPlayers() as $player){
            if($player->getPosition()->getId() == self::POINT_GUARD_POSITION_ID){
                $pointGuards[] = $player;
            };
        }

        return $pointGuards;
    }

    public function getShootingGuards(){
        $shootingGuards = new ArrayCollection();
        foreach($this->getPlayers() as $player){
            if($player->getPosition()->getId() == self::SHOOTING_GUARD_POSITION_ID){
                $shootingGuards[] = $player;
            };
        }

        return $shootingGuards;
    }

    public function getSmallForwards(){
        $smallForwards = new ArrayCollection();
        foreach($this->getPlayers() as $player){
            if($player->getPosition()->getId() == self::SMALL_FORWARD_POSITION_ID){
                $smallForwards[] = $player;
            };
        }

        return $smallForwards;
    }

    public function getPowerForwards(){
        $powerForwards = new ArrayCollection();
        foreach($this->getPlayers() as $player){
            if($player->getPosition()->getId() == self::POWER_FORWARD_POSITION_ID){
                $powerForwards[] = $player;
            };
        }

        return $powerForwards;
    }

    public function getCenters(){
        $centers = new ArrayCollection();
        foreach($this->getPlayers() as $player){
            if($player->getPosition()->getId() == self::CENTER_POSITION_ID){
                $centers[] = $player;
            };
        }

        return $centers;
    }
}

