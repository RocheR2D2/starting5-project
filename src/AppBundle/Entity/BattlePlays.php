<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BattlePlays
 *
 * @ORM\Table(name="battle_plays")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattlePlaysRepository")
 */
class BattlePlays
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battlePlays")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="battlePlays")
     * @ORM\JoinColumn(name="playerId", referencedColumnName="id")
     */
    private $playerId;

    /**
     * @var int
     *
     * @ORM\Column(name="pointsMade", type="integer", nullable=true)
     */
    private $pointsMade;

    /**
     * @ORM\ManyToOne(targetEntity="BattleRound", inversedBy="battlePlays")
     * @ORM\JoinColumn(name="battleRoundId", referencedColumnName="id")
     */
    private $battleRoundId;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCritical", type="boolean", nullable=true)
     */
    private $isCritical;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
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
     * @ORM\Column(name="bonus", type="integer", nullable=true)
     */
    private $bonus;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAttacker", type="boolean", nullable=true)
     */
    private $isAttacker;

    /**
     * @ORM\ManyToOne(targetEntity="BattlePlayers", inversedBy="battlePlays")
     * @ORM\JoinColumn(name="battlePlayerId", referencedColumnName="id")
     */
    private $battlePlayerId;
    /**
     * @var bool
     *
     * @ORM\Column(name="successfullDefense", type="boolean", nullable=true)
     */
    private $successfullDefense;

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
     * @return BattlePlays
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
     * Set playerId
     *
     * @param $playerId
     *
     * @return BattlePlays
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
     * Set pointsMade
     *
     * @param integer $pointsMade
     *
     * @return BattlePlays
     */
    public function setPointsMade($pointsMade)
    {
        $this->pointsMade = $pointsMade;

        return $this;
    }

    /**
     * Get pointsMade
     *
     * @return int
     */
    public function getPointsMade()
    {
        return $this->pointsMade;
    }

    /**
     * Set battleRoundId
     *
     * @param $battleRoundId
     *
     * @return BattlePlays
     */
    public function setBattleRoundId($battleRoundId)
    {
        $this->battleRoundId = $battleRoundId;

        return $this;
    }

    /**
     * Get battleRoundId
     *
     * @return int
     */
    public function getBattleRoundId()
    {
        return $this->battleRoundId;
    }

    /**
     * Set isCritical
     *
     * @param boolean $isCritical
     *
     * @return BattlePlays
     */
    public function setIsCritical($isCritical)
    {
        $this->isCritical = $isCritical;

        return $this;
    }

    /**
     * Get isCritical
     *
     * @return bool
     */
    public function getIsCritical()
    {
        return $this->isCritical;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BattlePlays
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param $rating
     *
     * @return BattlePlays
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }


    /**
     * @return bool
     */
    public function isAttacker()
    {
        return $this->isAttacker;
    }

    /**
     * @param $isAttacker
     * @return BattlePlays
     */
    public function setIsAttacker($isAttacker)
    {
        $this->isAttacker = $isAttacker;

        return $this;
    }

    /**
     * @return int
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * @param $bonus
     * @return BattlePlays
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBattlePlayerId()
    {
        return $this->battlePlayerId;
    }

    /**
     * @param $battlePlayerId
     * @return $this
     */
    public function setBattlePlayerId($battlePlayerId)
    {
        $this->battlePlayerId = $battlePlayerId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessfullDefense()
    {
        return $this->successfullDefense;
    }

    /**
     * @param $successfullDefense
     * @return $this
     */
    public function setSuccessfullDefense($successfullDefense)
    {
        $this->successfullDefense = $successfullDefense;

        return $this;
    }
}

