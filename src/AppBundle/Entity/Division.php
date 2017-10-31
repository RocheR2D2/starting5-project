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
     * @var int
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;


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
     * Set teamId
     *
     * @param integer $conferenceId
     *
     * @return Division
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
}

