<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * State
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StateRepository")
 */
class State
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
     * @ORM\Column(name="state_name", type="string", length=255)
     */
    private $stateName;

    /**
     * @var string
     *
     * @ORM\Column(name="slug_state", type="string", length=255)
     */
    private $slugState;

    /**
     *
     *
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="state")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $conference;

    /**
     *
     *
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="state")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     */
    private $division;


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
     * @param string $stateName
     *
     * @return State
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set slugState
     *
     * @param string $slugState
     *
     * @return State
     */
    public function setSlugState($slugState)
    {
        $this->slugState = $slugState;

        return $this;
    }

    /**
     * Get slugState
     *
     * @return string
     */
    public function getSlugState()
    {
        return $this->slugState;
    }

    /**
     * Set conference
     *
     * @return string
     */
    public function setConference($conference)
    {
        $this->conference = $conference;

        return $this->conference;
    }
    /**
     * Get conference
     *
     * @return string
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * Set division
     *
     * @return string
     */
    public function setDivision($division)
    {
        $this->division = $division;

        return $this->division;
    }
    /**
     * Get division
     *
     * @return string
     */
    public function getDivision()
    {
        return $this->division;
    }
}

