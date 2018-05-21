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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battlePlayers")
     * @ORM\JoinColumn(name="attackerId", referencedColumnName="id")
     */
    private $attackerId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="battlePlayers")
     * @ORM\JoinColumn(name="defenderId", referencedColumnName="id")
     */
    private $defenderId;

    /**
     * @var string
     *
     * @ORM\Column(name="score", type="string", nullable=true, length=255)
     */
    private $score;

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
     * Set score
     *
     * @param string $score
     *
     * @return BattleRound
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string
     */
    public function getScore()
    {
        return $this->score;
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
     * @param mixed $defenderId
     */
    public function setDefenderId($defenderId)
    {
        $this->defenderId = $defenderId;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * @param bool $done
     */
    public function setDone($done)
    {
        $this->done = $done;
    }
}

