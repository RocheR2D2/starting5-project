<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Division
 *
 * @ORM\Table(name="division")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DivisionRepository")
 */
class Division
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
     * @ORM\Column(name="slug_division", type="string", length=255)
     */
    private $slugDivision;

    /**
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="division")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $conference;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="division", fetch="EAGER")
     */
    private $team;


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
     * @return Division
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
     * Set slugDivision
     *
     * @param string $slugDivision
     *
     * @return Division
     */
    public function setSlugDivision($slugDivision)
    {
        $this->slugDivision = $slugDivision;

        return $this;
    }

    /**
     * Get slugDivision
     *
     * @return string
     */
    public function getSlugDivision()
    {
        return $this->slugDivision;
    }

    /**
     * @param $conference
     * @return $this
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
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }
}

