<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserTrainer
 *
 * @ORM\Table(name="user_trainer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserTrainerRepository")
 */
class UserTrainer
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userTrainer")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="userTrainer")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private $trainerId;


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
     * @return UserTrainer
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
     * Set trainerId
     *
     * @param integer $trainerId
     *
     * @return UserTrainer
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
}

