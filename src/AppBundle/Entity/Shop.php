<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table(name="shop")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopRepository")
 */
class Shop
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
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="shop")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    public $playerId;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    public $price;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    public $type;

    /**
     * @var boolean $isMyPlayer
     */
    public $isMyPlayer;

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
     * @return Shop
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
     * Set price
     *
     * @param integer $price
     *
     * @return Shop
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Shop
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function getisMyPlayer()
    {
        return $this->isMyPlayer;
    }

    /**
     * @param boolean $isMyPlayer
     */
    public function setIsMyPlayer($isMyPlayer)
    {
        $this->isMyPlayer = $isMyPlayer;
    }
}

