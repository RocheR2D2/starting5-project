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
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="battlePlayers")
     * @ORM\JoinColumn(name="playerId", referencedColumnName="id")
     */
    private $playerId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battlePlayers")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
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
     * @ORM\ManyToOne(targetEntity="Battle", inversedBy="battlePlayers")
     * @ORM\JoinColumn(name="battleId", referencedColumnName="id")
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
     * @param $playerId
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
     * @param $userId
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
     * @param $battleId
     *
     * @return BattlePlayers
     */
    public function setBattleId($battleId)
    {
        $this->battleId = $battleId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBattleId()
    {
        return $this->battleId;
    }
}

