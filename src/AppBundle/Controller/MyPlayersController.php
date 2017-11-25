<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Repository\NBAPlayersRepository;
use AppBundle\Repository\UsersPlayersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyPlayersController extends Controller
{
    public function indexAction(Request $request)
    {
        $userPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $playersView = $this->playersAction();
        $count = count($userPlayers->findBy(['userId' => $this->getUser()]));

            return $this->render('starting5/dashboard/players/index.html.twig', [
            'name' => "Starting 5",
            'players' => $playersView,
            'count' => $count
        ]);
    }

    public function playersAction()
    {
        $userPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $nbaPlayers = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $myPlayers = $this->getMyPlayers($this->getUser(), $userPlayers, $nbaPlayers, 0);

        return $this->render('starting5/dashboard/players/players.html.twig', [
            'myPlayers' => $myPlayers,
        ]);
    }

    public function guardAction()
    {
        $userPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $nbaPlayers = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $myPlayers = $this->getMyGuards($this->getUser(), $userPlayers, $nbaPlayers);

        return $this->render('starting5/dashboard/players/guard.html.twig', [
            'name' => "Starting 5",
            'guards' => $myPlayers,
        ]);
    }

    public function forwardAction()
    {
        $userPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $nbaPlayers = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $myPlayers = $this->getMyForwards($this->getUser(), $userPlayers, $nbaPlayers);

        return $this->render('starting5/dashboard/players/forward.html.twig', [
            'name' => "Starting 5",
            'forwards' => $myPlayers,
        ]);
    }

    public function centerAction()
    {
        $userPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $nbaPlayers = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $myPlayers = $this->getMyCenters($this->getUser(), $userPlayers, $nbaPlayers);

        return $this->render('starting5/dashboard/players/center.html.twig', [
            'name' => "Starting 5",
            'centers' => $myPlayers,
        ]);
    }

    public function getMyPlayers($user, $userTeamRepository, $playerRepository, $current = 1)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user], null, 9, 0);
        if($current > 0){
            $userPlayers = $userTeamRepository->findBy(['userId' => $user], null, 9, 9 * $current);
        }
        $myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getPlayerId();
            $personId = $myPlayer->getPlayerId()->getId();
            $isDuplicate = $userTeamRepository->findBy(['playerId' => $personId]);
            $this->checkDuplicatedPlayers(count($isDuplicate), $personId, $userTeamRepository);
            $player = $playerRepository->getProfile($playerId);
            $myPlayers[] = $player;
        }

        return $myPlayers;
    }

    public function checkDuplicatedPlayers($count, $personId, $userTeamRepository)
    {
        if ($count >= 2) {
            $playerToDelete = $userTeamRepository->findOneBy(['playerId' => $personId]);
            $user = $this->getUser();
            $user->setQuizPoints($user->getQuizPoints() + 50);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->remove($playerToDelete);
            $em->flush();
        }
    }

    public function getMyGuards($user, $userTeamRepository, $playerRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user]);
        $myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getPlayerId();
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
            $playerId = $myPlayer->getPlayerId()->getPlayerId();
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
            $playerId = $myPlayer->getPlayerId()->getPlayerId();
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

    public function discardPlayerAction(Request $request)
    {
        $nbaPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $playerId = $data = $request->request->get('userPlayerId');
        $player = $nbaPlayers->findOneBy(['playerId' => $playerId]);

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->setQuizPoints($user->getQuizPoints() + 25);
        $em->persist($user);
        $em->remove($player);
        $em->flush();
        $em->clear();

        $response = new Response($this->playersAjax());

        return $response;
    }

    public function playersAjax($page = 1)
    {
        $userPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $nbaPlayers = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $myPlayers = $this->getMyPlayers($this->getUser(), $userPlayers, $nbaPlayers, $page);

        return $this->renderView('starting5/dashboard/players/players.html.twig', [
            'myPlayers' => $myPlayers,
        ]);
    }

    public function playersNextAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        $response = new Response($this->playersAjax($page));

        return $response;
    }

    public function playersPreviousAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        $response = new Response($this->playersAjax($page));

        return $response;
    }
}
