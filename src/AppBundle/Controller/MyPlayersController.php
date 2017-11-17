<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MyPlayersController extends Controller
{
    public function indexAction(Request $request)
    {
        $playerRepository = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userTeamRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $myPlayers = $this->getMyPlayers($this->getUser(), $userTeamRepository, $playerRepository);

        return $this->render('starting5/dashboard/players/index.html.twig', [
            'name' => "Starting 5",
            'myPlayers' => $myPlayers,
        ]);
    }

    public function guardAction()
    {
        $playerRepository = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userTeamRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $myPlayers = $this->getMyGuards($this->getUser(), $userTeamRepository, $playerRepository);

        return $this->render('starting5/dashboard/players/guard.html.twig', [
            'name' => "Starting 5",
            'guards' => $myPlayers,
        ]);
    }

    public function forwardAction()
    {
        $playerRepository = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userTeamRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $myPlayers = $this->getMyForwards($this->getUser(), $userTeamRepository, $playerRepository);

        return $this->render('starting5/dashboard/players/forward.html.twig', [
            'name' => "Starting 5",
            'forwards' => $myPlayers,
        ]);
    }

    public function centerAction()
    {
        $playerRepository = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userTeamRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $myPlayers = $this->getMyCenters($this->getUser(), $userTeamRepository, $playerRepository);

        return $this->render('starting5/dashboard/players/center.html.twig', [
            'name' => "Starting 5",
            'centers' => $myPlayers,
        ]);
    }

    public function getMyPlayers($user, $userTeamRepository, $playerRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user]);
        $myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getId();
            $player = $playerRepository->getProfile($playerId);
            $myPlayers[] = $player;
        }

        return $myPlayers;
    }

    public function getMyGuards($user, $userTeamRepository, $playerRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user]);
        $myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getId();
            $player = $playerRepository->getProfile($playerId);
            $playerPosition = explode('-', $player['pos']);
            if (isset($playerPosition[0]) && $playerPosition[0] == 'G') {
                    $myPlayers[] = $player;
            } elseif (isset($playerPosition[0]) && isset($playerPosition[1]) && $playerPosition[1] == 'G') {
                    $myPlayers[] = $player;
            }
        }

        return $myPlayers;
    }

    public function getMyForwards($user, $userTeamRepository, $playerRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user]);
        $myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getId();
            $player = $playerRepository->getProfile($playerId);
            $playerPosition = explode('-', $player['pos']);
            if (isset($playerPosition[0]) && $playerPosition[0] == 'F') {
                $myPlayers[] = $player;
            } elseif (isset($playerPosition[0]) && isset($playerPosition[1]) && $playerPosition[1] == 'F') {
                $myPlayers[] = $player;
            }
        }

        return $myPlayers;
    }

    public function getMyCenters($user, $userTeamRepository, $playerRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user]);
        $myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getId();
            $player = $playerRepository->getProfile($playerId);
            $playerPosition = explode('-', $player['pos']);
            if (isset($playerPosition[0]) && $playerPosition[0] == 'C') {
                $myPlayers[] = $player;
            } elseif (isset($playerPosition[0]) && isset($playerPosition[1]) && $playerPosition[1] == 'C') {
                $myPlayers[] = $player;
            }
        }

        return $myPlayers;
    }
}
