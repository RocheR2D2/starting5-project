<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stadium
 *
 * @ORM\Table(name="stadium")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StadiumRepository")
 */
class Stadium
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
     * @ORM\Column(name="slug_stadium", type="string", length=255)
     */
    private $slugStadium;


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
     * @return Stadium
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
     * Set slug stadium
     *
     * @param string $slugStadium
     *
     * @return Stadium
     */
    public function setSlugStadium($slugStadium)
    {
        $this->slugStadium = $slugStadium;

        return $this;
    }

    /**
     * Get slugStadium
     *
     * @return string
     */
    public function getSlugStadium()
    {
        return $this->slugStadium;
    }
}

