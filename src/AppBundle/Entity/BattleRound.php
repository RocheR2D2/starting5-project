<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BattleRound
 *
 * @ORM\Table(name="battle_round")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattleRoundRepository")
 */
class BattleRound
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
     * @ORM\ManyToOne(targetEntity="Battle", inversedBy="battleRound")
     * @ORM\JoinColumn(name="battleId", referencedColumnName="id")
     */
    private $battleId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battleRound")
     * @ORM\JoinColumn(name="attackerId", referencedColumnName="id")
     */
    private $attackerId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battleRound")
     * @ORM\JoinColumn(name="defenderId", referencedColumnName="id")
     */
    private $defenderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="attackerPoints", type="integer", nullable=true)
     */
    private $attackerPoints;

    /**
     * @var integer
     *
     * @ORM\Column(name="defenderPoints", type="integer", nullable=true)
     */
    private $defenderPoints;

    /**
     * @var integer
     *
     * @ORM\Column(name="round", type="integer")
     */
    private $round;

    /**
     * @var boolean
     *
     * @ORM\Column(name="done", type="boolean", nullable=true)
     */
    private $done;
    /**
     * @ORM\ManyToOne(targetEntity="PlayType", inversedBy="battleRound")
     * @ORM\JoinColumn(name="playType", referencedColumnName="id")
     */
    private $playType;

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
     * Set battleId
     *
     * @param integer $battleId
     *
     * @return BattleRound
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

    /**
     * Set attackerId
     *
     * @param integer $attackerId
     *
     * @return BattleRound
     */
    public function setAttackerId($attackerId)
    {
        $this->attackerId = $attackerId;

        return $this;
    }

    /**
     * Get attackerId
     *
     * @return int
     */
    public function getAttackerId()
    {
        return $this->attackerId;
    }

    /**
     * Set round
     *
     * @param integer $round
     *
     * @return BattleRound
     */
    public function setRound($round)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get typeOfRound
     *
     * @return integer
     */
    public function getRound()
    {
        return $this->round;
    }
    /**
     * @return mixed
     */
    public function getDefenderId()
    {
        return $this->defenderId;
    }

    /**
     * @param $defenderId
     * @return $this
     */
    public function setDefenderId($defenderId)
    {
        $this->defenderId = $defenderId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * @param $done
     * @return $this
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlayType()
    {
        return $this->playType;
    }

    /**
     * @param $playType
     * @return mixed
     */
    public function setPlayType($playType)
    {
        return $this->playType = $playType;
    }

    /**
     * @return int
     */
    public function getAttackerPoints()
    {
        if(!$this->attackerPoints) {
            return '0';
        }

        return $this->attackerPoints;
    }

    /**
     * @param $attackerPoints
     * @return $this
     */
    public function setAttackerPoints($attackerPoints)
    {
        $this->attackerPoints = $attackerPoints;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefenderPoints()
    {
        if(!$this->defenderPoints) {
            return '0';
        }

        return $this->defenderPoints;
    }

    /**
     * @param $defenderPoints
     * @return $this
     */
    public function setDefenderPoints($defenderPoints)
    {
        $this->defenderPoints = $defenderPoints;

        return $this;
    }
}

