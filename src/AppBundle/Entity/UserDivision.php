<?php

namespace AppBundle\Entity;

use Beta\A;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserDivision
 *
 * @ORM\Table(name="user_division")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserDivisionRepository")
 */
class UserDivision
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="UserDivision")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="UserDivision")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     */
    private $division;

    const POINT_GUARD_POSITION_ID = 1;
    const SHOOTING_GUARD_POSITION_ID = 2;
    const SMALL_FORWARD_POSITION_ID = 3;
    const POWER_FORWARD_POSITION_ID = 4;
    const CENTER_POSITION_ID = 5;


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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param mixed $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }
}

