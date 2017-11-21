<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NBAPlayers
 *
 * @ORM\Table(name="n_b_a_players")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NBAPlayersRepository")
 */
class NBAPlayers
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
     * @ORM\Column(name="player_id", type="integer", unique=true)
     */
    private $playerId;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

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
     * @return NBAPlayers
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
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }
    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
}

