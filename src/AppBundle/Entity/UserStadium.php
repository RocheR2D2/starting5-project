<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserStadium
 *
 * @ORM\Table(name="user_stadium")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserStadiumRepository")
 */
class UserStadium
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userStadium")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Stadium", inversedBy="userStadium")
     * @ORM\JoinColumn(name="stadium_id", referencedColumnName="id")
     */
    private $stadiumId;


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
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserStadium
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set stadiumId
     *
     * @param integer $stadiumId
     *
     * @return UserStadium
     */
    public function setStadiumId($stadiumId)
    {
        $this->stadiumId = $stadiumId;

        return $this;
    }

    /**
     * Get stadiumId
     *
     * @return int
     */
    public function getStadiumId()
    {
        return $this->stadiumId;
    }
}

