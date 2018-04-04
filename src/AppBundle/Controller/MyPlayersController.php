<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyPlayersController extends Controller
{
    protected $userPlayers;
    protected $nbaPlayers;
    private $em;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
        $this->userPlayers = $this->em->getRepository(UsersPlayers::class);
        $this->nbaPlayers = $this->em->getRepository(NBAPlayers::class);
    }

    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $playersView = $this->playersAction();
        $count = count($this->userPlayers->findBy(['userId' => $this->getUser()]));

            return $this->render('starting5/dashboard/players/index.html.twig', [
            'name' => "Starting 5",
            'players' => $playersView,
            'count' => $count
        ]);
    }

    public function playersAction()
    {
        return $this->render('starting5/dashboard/players/players.html.twig', [
            'myPlayers' => $this->getMyPlayers(),
        ]);
    }

    public function guardAction()
    {
        $myPlayers = $this->getMyGuards($this->getUser(), $this->userPlayers, $this->nbaPlayers);

        return $this->render('starting5/dashboard/players/guard.html.twig', [
            'name' => "Starting 5",
            'guards' => $myPlayers,
        ]);
    }

    public function forwardAction()
    {
        $myPlayers = $this->getMyForwards($this->getUser(), $this->userPlayers, $this->nbaPlayers);

        return $this->render('starting5/dashboard/players/forward.html.twig', [
            'name' => "Starting 5",
            'forwards' => $myPlayers,
        ]);
    }

    public function centerAction()
    {
        $myPlayers = $this->getMyCenters($this->getUser(), $this->userPlayers, $this->nbaPlayers);

        return $this->render('starting5/dashboard/players/center.html.twig', [
            'name' => "Starting 5",
            'centers' => $myPlayers,
        ]);
    }

    public function getMyPlayers($current = 0)
    {
        $userPlayers = $this->userPlayers->findBy(['userId' => $this->getUser()], ['rating' => 'DESC'], 9, 0);
        if($current > 0){
            $userPlayers = $this->userPlayers->findBy(['userId' => $this->getUser()], null, 9, 9 * $current);
        }
        /*$myPlayers = new ArrayCollection();
        foreach ($userPlayers as $myPlayer) {
            $playerId = $myPlayer->getPlayerId()->getPlayerId();
            $personId = $myPlayer->getPlayerId()->getId();
            $isDuplicate = $this->userPlayers->findBy(['playerId' => $personId]);
            $this->checkDuplicatedPlayers(count($isDuplicate), $personId, $this->userPlayers);
            $player = $this->nbaPlayers->getProfile($playerId);
            $myPlayers[] = $player;
        }*/

        return $userPlayers;
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

    public function getMyGuards($user, $userTeamRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user, 'position' => ['G' ,'F-G','G-F']]);

        return $userPlayers;
    }

    public function getMyForwards($user, $userTeamRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user, 'position' => ['F' ,'F-C','C-F']]);

        return $userPlayers;
    }

    public function getMyCenters($user, $userTeamRepository)
    {
        $userPlayers = $userTeamRepository->findBy(['userId' => $user, 'position' => ['C' ,'F-C','C-F']]);

        return $userPlayers;
    }

    public function discardPlayerAction(Request $request)
    {
        //$nbaPlayers = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $playerId = $data = $request->request->get('userPlayerId');
        $page = $data = $request->request->get('page');
        $player = $this->userPlayers->findOneBy(['playerId' => $playerId]);

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->setQuizPoints($user->getQuizPoints() + 25);
        $em->persist($user);
        $em->remove($player);
        $em->flush();
        $em->clear();

        $response = new Response($this->playersAjax($page));

        return $response;
    }

    public function playersAjax($page = 1)
    {
        $myPlayers = $this->getMyPlayers($page);

        return $this->renderView('starting5/dashboard/players/players.html.twig', [
            'myPlayers' => $myPlayers,
        ]);
    }

    public function playersNextAction(Request $request)
    {
        $page = $request->request->get('page');
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
