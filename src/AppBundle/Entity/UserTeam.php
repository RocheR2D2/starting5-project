<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserTeam
 *
 * @ORM\Table(name="user_team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserTeamRepository")
 */
class UserTeam
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="userTeam")
     * @ORM\JoinColumn(name="point_guard", referencedColumnName="id")
     */
    public $pointGuard;
    /**
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="userTeam")
     * @ORM\JoinColumn(name="shooting_guard", referencedColumnName="id")
     */
    public $shootingGuard;
    /**
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="userTeam")
     * @ORM\JoinColumn(name="power_forward", referencedColumnName="id")
     */
    public $powerForward;
    /**
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="userTeam")
     * @ORM\JoinColumn(name="small_forward", referencedColumnName="id")
     */
    public $smallForward;
    /**
     * @ORM\ManyToOne(targetEntity="NBAPlayers", inversedBy="userTeam")
     * @ORM\JoinColumn(name="center", referencedColumnName="id")
     */
    public $center;
    /**
     * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="userTeam")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private $trainerId;
    /**
     * @ORM\ManyToOne(targetEntity="Stadium", inversedBy="userTeam")
     * @ORM\JoinColumn(name="stadium_id", referencedColumnName="id")
     */
    private $stadiumId;
    /**
     * @var int
     *
     * @ORM\Column(name="team_like", type="integer")
     */
    private $like;
    /**
     * @var int
     *
     * @ORM\Column(name="team_dislike", type="integer")
     */
    private $dislike;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userTeam")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     * @var int
     *
     * @ORM\Column(name="team_rating", type="integer")
     */
    private $teamRating;
    /**
     * @var int
     *
     * @ORM\Column(name="off_rating", type="integer")
     */
    private $offRating;
    /**
     * @var int
     *
     * @ORM\Column(name="def_rating", type="integer")
     */
    private $defRating;

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
     * Set name
     *
     * @param string $name
     *
     * @return UserTeam
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPointGuard()
    {
        return $this->pointGuard;
    }

    /**
     * @param mixed $pointGuard
     */
    public function setPointGuard($pointGuard)
    {
        $this->pointGuard = $pointGuard;
    }

    /**
     * @return mixed
     */
    public function getShootingGuard()
    {
        return $this->shootingGuard;
    }

    /**
     * @param mixed $shootingGuard
     */
    public function setShootingGuard($shootingGuard)
    {
        $this->shootingGuard = $shootingGuard;
    }

    /**
     * @return mixed
     */
    public function getPowerForward()
    {
        return $this->powerForward;
    }

    /**
     * @param mixed $powerForward
     */
    public function setPowerForward($powerForward)
    {
        $this->powerForward = $powerForward;
    }

    /**
     * @return mixed
     */
    public function getSmallForward()
    {
        return $this->smallForward;
    }

    /**
     * @param mixed $smallForward
     */
    public function setSmallForward($smallForward)
    {
        $this->smallForward = $smallForward;
    }

    /**
     * @return mixed
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * @param mixed $center
     */
    public function setCenter($center)
    {
        $this->center = $center;
    }

    /**
     * @return mixed
     */
    public function getTrainerId()
    {
        return $this->trainerId;
    }

    /**
     * @param mixed $trainerId
     */
    public function setTrainerId($trainerId)
    {
        $this->trainerId = $trainerId;
    }

    /**
     * @return mixed
     */
    public function getStadiumId()
    {
        return $this->stadiumId;
    }

    /**
     * @param mixed $stadiumId
     */
    public function setStadiumId($stadiumId)
    {
        $this->stadiumId = $stadiumId;
    }

    /**
     * @return int
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     * @param int $like
     */
    public function setLike($like)
    {
        $this->like = $like;
    }

    /**
     * @return int
     */
    public function getDislike()
    {
        return $this->dislike;
    }

    /**
     * @param int $dislike
     */
    public function setDislike($dislike)
    {
        $this->dislike = $dislike;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getTeamRating()
    {
        return $this->teamRating;
    }

    public function getOffRating()
    {
        return $this->offRating;
    }

    public function getDefRating()
    {
        return $this->defRating;
    }
    /**
     * @param int $teamRating
     */
    public function setTeamRating($teamRating)
    {
        $this->teamRating = $teamRating;
    }

    /**
     * @param int $offRating
     */
    public function setOffRating($offRating)
    {
        $this->offRating = $offRating;
    }

    /**
     * @param int $defRating
     */
    public function setDefRating($defRating)
    {
        $this->defRating = $defRating;
    }
}

