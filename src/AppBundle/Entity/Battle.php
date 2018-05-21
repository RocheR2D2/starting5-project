<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Battle
 *
 * @ORM\Table(name="battle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattleRepository")
 */
class Battle
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battle")
     * @ORM\JoinColumn(name="playerOneId", referencedColumnName="id")
     */
    private $playerOneId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battle")
     * @ORM\JoinColumn(name="playerTwoId", referencedColumnName="id")
     */
    private $playerTwoId;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battle")
     * @ORM\JoinColumn(name="winnerUserId", referencedColumnName="id")
     */
    private $winnerUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="playerOneScore", type="integer")
     */
    private $playerOneScore;

    /**
     * @var int
     *
     * @ORM\Column(name="playerTwoScore", type="integer")
     */
    private $playerTwoScore;


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
     * Set playerOneId
     *
     * @param $playerOneId
     *
     * @return Battle
     */
    public function setPlayerOneId($playerOneId)
    {
        $this->playerOneId = $playerOneId;

        return $this;
    }

    /**
     * Get playerOneId
     *
     * @return int
     */
    public function getPlayerOneId()
    {
        return $this->playerOneId;
    }

    /**
     * Set playerTwoId
     *
     * @param $playerTwoId
     *
     * @return Battle
     */
    public function setPlayerTwoId($playerTwoId)
    {
        $this->playerTwoId = $playerTwoId;

        return $this;
    }

    /**
     * Get playerTwoId
     *
     * @return int
     */
    public function getPlayerTwoId()
    {
        return $this->playerTwoId;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Battle
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set winnerUserId
     *
     * @param integer $winnerUserId
     *
     * @return Battle
     */
    public function setWinnerUserId($winnerUserId)
    {
        $this->winnerUserId = $winnerUserId;

        return $this;
    }

    /**
     * Get winnerUserId
     *
     * @return int
     */
    public function getWinnerUserId()
    {
        return $this->winnerUserId;
    }

    /**
     * Set playerOneScore
     *
     * @param integer $playerOneScore
     *
     * @return Battle
     */
    public function setPlayerOneScore($playerOneScore)
    {
        $this->playerOneScore = $playerOneScore;

        return $this;
    }

    /**
     * Get playerOneScore
     *
     * @return int
     */
    public function getPlayerOneScore()
    {
        return $this->playerOneScore;
    }

    /**
     * Set playerTwoScore
     *
     * @param integer $playerTwoScore
     *
     * @return Battle
     */
    public function setPlayerTwoScore($playerTwoScore)
    {
        $this->playerTwoScore = $playerTwoScore;

        return $this;
    }

    /**
     * Get playerTwoScore
     *
     * @return int
     */
    public function getPlayerTwoScore()
    {
        return $this->playerTwoScore;
    }
}

