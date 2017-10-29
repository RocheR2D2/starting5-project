<?php

namespace AppBundle\Entity;

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
     * @var int
     *
     * @ORM\Column(name="division_id", type="integer")
     */
    private $divisionId;

    /**
     * @var int
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;

    /**
     * @var int
     *
     * @ORM\Column(name="state_id", type="integer")
     */
    private $stateId;

    /**
     * @var int
     *
     * @ORM\Column(name="stadium_id", type="integer")
     */
    private $stadiumId;

    /**
     * @var int
     *
     * @ORM\Column(name="trainer_id", type="integer")
     */
    private $trainerId;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=255)
     */
    private $town;


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
     * Set divisionId
     *
     * @param integer $divisionId
     *
     * @return Team
     */
    public function setDivisionId($divisionId)
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * Get divisionId
     *
     * @return int
     */
    public function getDivisionId()
    {
        return $this->divisionId;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     *
     * @return Team
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;

        return $this;
    }

    /**
     * Get conferenceId
     *
     * @return int
     */
    public function getConferenceId()
    {
        return $this->conferenceId;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     *
     * @return Team
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Get stateId
     *
     * @return int
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set stadiumId
     *
     * @param integer $stadiumId
     *
     * @return Team
     */
    public function setStadiumId($stadiumId)
    {
        $this->stadiumId = $stadiumId;

        return $this;
    }

    /**
     * Get statiumId
     *
     * @return int
     */
    public function getStatiumId()
    {
        return $this->statiumId;
    }

    /**
     * Set trainerId
     *
     * @param integer $trainerId
     *
     * @return Team
     */
    public function setTrainerId($trainerId)
    {
        $this->trainerId = $trainerId;

        return $this;
    }

    /**
     * Get trainerId
     *
     * @return int
     */
    public function getTrainerId()
    {
        return $this->trainerId;
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
}

