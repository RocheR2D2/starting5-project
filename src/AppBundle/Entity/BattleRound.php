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
     * @var int
     *
     * @ORM\Column(name="battleId", type="integer", nullable=true)
     */
    private $battleId;

    /**
     * @var int
     *
     * @ORM\Column(name="attackerId", type="integer", nullable=true)
     */
    private $attackerId;

    /**
     * @var string
     *
     * @ORM\Column(name="score", type="string", length=255)
     */
    private $score;

    /**
     * @var string
     *
     * @ORM\Column(name="typeOfRound", type="string", length=255)
     */
    private $typeOfRound;


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
     * Set typeOfRound
     *
     * @param string $typeOfRound
     *
     * @return BattleRound
     */
    public function setTypeOfRound($typeOfRound)
    {
        $this->typeOfRound = $typeOfRound;

        return $this;
    }

    /**
     * Get typeOfRound
     *
     * @return string
     */
    public function getTypeOfRound()
    {
        return $this->typeOfRound;
    }
}

