<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BattlePlayers
 *
 * @ORM\Table(name="battle_players")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattlePlayersRepository")
 */
class BattlePlayers
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
     * @var int
     *
     * @ORM\Column(name="playerId", type="integer", nullable=true)
     */
    private $playerId;

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer", nullable=true)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="actionPoint", type="integer", nullable=true)
     */
    private $actionPoint;

    /**
     * @var int
     *
     * @ORM\Column(name="battleId", type="integer", nullable=true)
     */
    private $battleId;


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
     * Set playerId
     *
     * @param integer $playerId
     *
     * @return BattlePlayers
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId
     *
     * @return int
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return BattlePlayers
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
     * Set position
     *
     * @param string $position
     *
     * @return BattlePlayers
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return BattlePlayers
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set actionPoint
     *
     * @param integer $actionPoint
     *
     * @return BattlePlayers
     */
    public function setActionPoint($actionPoint)
    {
        $this->actionPoint = $actionPoint;

        return $this;
    }

    /**
     * Get actionPoint
     *
     * @return int
     */
    public function getActionPoint()
    {
        return $this->actionPoint;
    }

    /**
     * Set battleId
     *
     * @param integer $battleId
     *
     * @return BattlePlayers
     */
    public function setBattleId($battleId)
    {
        $this->battleId = $battleId;

        return $this;
    }

    /**
     * Get battleId
     *
     * @return int
     */
    public function getBattleId()
    {
        return $this->battleId;
    }
}

