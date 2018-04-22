<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Entity\UserTeam;
use AppBundle\Form\UserTeamType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $userTeamDoctrine = $this->getDoctrine()->getRepository(UserTeam::class);
        $playerRepository = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userTeams = $userTeamDoctrine->findBy(['user' => $user]);
        $userTeamsDatas = new ArrayCollection();

        foreach ($userTeams as $userTeam) {
            $teams = new ArrayCollection();
            $players = new ArrayCollection();
            $teamInfos = new ArrayCollection();

            $pointGuard = $this->getPointGuard($userTeam, $playerRepository);
            $shootingGuard = $this->getShootingGuard($userTeam, $playerRepository);
            $smallForward = $this->getSmallForward($userTeam, $playerRepository);
            $powerForward = $this->getPowerForward($userTeam, $playerRepository);
            $center = $this->getCenter($userTeam, $playerRepository);

            $players = $this->setPlayers($players, $pointGuard, $shootingGuard, $smallForward, $powerForward, $center);
            $teamInfos = $this->setTeamData($teamInfos, $userTeam->getName(), $userTeam->getTrainerId(), $userTeam->getStadiumId(), $userTeam->getLike(), $userTeam->getDislike(), $userTeam->getId());

            $teams['players'] = $players;
            $teams['data'] = $teamInfos;

            $userTeamsDatas[] = $teams;
        }
        $countUserTeam = count($userTeamsDatas);

        return $this->render('starting5/dashboard/index.html.twig', [
            'name' => "Starting 5",
            'userTeams' => $userTeams,
            'teams' => $userTeamsDatas,
            'countTeam' => $countUserTeam
        ]);
    }

    public function newAction(Request $request)
    {
        return $this->render('starting5/dashboard/new.html.twig');
    }

    public function getPlayerAction(Request $request)
    {

        $user = $this->getUser();

        $userTeamRepository = $this->getDoctrine()->getRepository(UserTeam::class);
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $MyPlayers = $userRepository->getMyPlayers($user);
        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $serializer = $this->container->get('serializer');

        $guards = $userRepository->getGuards($user);
        $guardsJson = $serializer->serialize($MyPlayers, 'json');

        $gCount = $userRepository->allGuards;
        $forwards = $userRepository->getForwards($user);
        $forwardsJson = $serializer->serialize($forwards, 'json');
        $fCount = $userRepository->allForwards;
        $centers = $userRepository->getCenters($user);
        $centersJson = $serializer->serialize($centers, 'json');
        $cCount = $userRepository->allCenters;
        $userTeams = $userTeamRepository->findBy(['user' => $user]);

        $pg = 'No Player Selected';
        $sg = 'No Player Selected';
        $sf = 'No Player Selected';
        $pf = 'No Player Selected';
        $c = 'No Player Selected';

        $allPLayers = array_merge($guards, $forwards, $centers);
        $result = $serializer->serialize($allPLayers, 'json');
        return new Response($result);

    }


    public function createTeamAction(Request $request)
    {
        $players = $request->getContent();
        $players = json_decode($players, true);
        $user = $this->getUser();
        $userTeam = new UserTeam();
        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $this->setNewPlayers($userTeam, $playerDoctrine, $players);
        $userTeam->setUser($user);
        $userTeam->setLike(0);
        $userTeam->setDislike(0);
        //Set trainder + stadium here
        $em = $this->getDoctrine()->getManager();
        $em->persist($userTeam);
        $em->flush();

        return new Response("done");

    }

    public function editAction(Request $request, $id)
    {
        $user = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $userTeamRepository = $this->getDoctrine()->getRepository(UserTeam::class);
        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userTeam = $userTeamRepository->find($id);
        $form = $this->createForm(UserTeamType::class, $userTeam);

        $pg = $userTeamRepository->find($id)->getPointGuard()->getFullName();
        $sg = $userTeamRepository->find($id)->getShootingGuard()->getFullName();
        $sf = $userTeamRepository->find($id)->getSmallForward()->getFullName();
        $pf = $userTeamRepository->find($id)->getPowerForward()->getFullName();
        $c = $userTeamRepository->find($id)->getCenter()->getFullName();

        $guards = $userRepository->getGuards($user);
        $gCount = $userRepository->allGuards;
        $forwards = $userRepository->getForwards($user);
        $fCount = $userRepository->allForwards;
        $centers = $userRepository->getCenters($user);
        $cCount = $userRepository->allCenters;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('form');
            $this->setNewPlayers($userTeam, $playerDoctrine, $data);
            $em = $this->getDoctrine()->getManager();
            $userTeam = $form->getData();
            $em->persist($userTeam);
            $em->flush();

            return $this->redirectToRoute('user.team.edit', ['id' => $id]);
        }

        return $this->render('starting5/dashboard/teams/edit.html.twig', array(
            'form' => $form->createView(),
            'userTeam' => $userTeam,
            'id' => $id,
            'guards' => $guards,
            'gCount' => $gCount,
            'forwards' => $forwards,
            'centers' => $centers,
            'fCount' => $fCount,
            'cCount' => $cCount,
            'pg' => $pg,
            'sg' => $sg,
            'sf' => $sf,
            'pf' => $pf,
            'c' => $c,
        ));
    }

    public function getPointGuard($userTeam, $playerRepository)
    {
        $poingGuardId = $userTeam->getPointGuard()->getId();
        $pointGuardProfileId = $playerRepository->findOneBy(['id' => $poingGuardId])->getPlayerId();
        $pointGuard = $playerRepository->getProfile($pointGuardProfileId);

        return $pointGuard;
    }

    public function getShootingGuard($userTeam, $playerRepository)
    {
        $shootingGuardId = $userTeam->getShootingGuard()->getId();
        $shootingGuardProfileId = $playerRepository->findOneBy(['id' => $shootingGuardId])->getPlayerId();
        $shootingGuard = $playerRepository->getProfile($shootingGuardProfileId);

        return $shootingGuard;
    }

    public function getSmallForward($userTeam, $playerRepository)
    {
        $smallForwardId = $userTeam->getSmallForward()->getId();
        $smallForwardProfileId = $playerRepository->findOneBy(['id' => $smallForwardId])->getPlayerId();
        $smallForward = $playerRepository->getProfile($smallForwardProfileId);

        return $smallForward;
    }

    public function getPowerForward($userTeam, $playerRepository)
    {
        $powerForwardId = $userTeam->getPowerForward()->getId();
        $powerForwardProfileId = $playerRepository->findOneBy(['id' => $powerForwardId])->getPlayerId();
        $powerForward = $playerRepository->getProfile($powerForwardProfileId);

        return $powerForward;
    }

    public function getCenter($userTeam, $playerRepository)
    {
        $centerId = $userTeam->getCenter()->getId();
        $centerProfileId = $playerRepository->findOneBy(['id' => $centerId])->getPlayerId();
        $center = $playerRepository->getProfile($centerProfileId);

        return $center;
    }

    public function setPlayers($players, $pointGuard, $shootingGuard, $smallForward, $powerForward, $center)
    {
        $players['pointGuard'] = $pointGuard;
        $players['shootingGuard'] = $shootingGuard;
        $players['smallForward'] = $smallForward;
        $players['powerForward'] = $powerForward;
        $players['center'] = $center;

        return $players;
    }

    public function setTeamData($teamInfos, $name, $trainer, $stadium, $like, $dislike, $user)
    {
        $teamInfos['name'] = $name;
        $teamInfos['trainer'] = $trainer;
        $teamInfos['stadium'] = $stadium;
        $teamInfos['like'] = $like;
        $teamInfos['dislike'] = $dislike;
        $teamInfos['id'] = $user;

        return $teamInfos;
    }

    public function setNewPlayers($userTeam, $playerDoctrine, $data)
    {
        $userTeam->setPointGuard($playerDoctrine->findOneBy(['playerId' => $data['pointGuard']["playerId"]]));
        $userTeam->setShootingGuard($playerDoctrine->findOneBy(['playerId' => $data['shootingGuard']["playerId"]]));
        $userTeam->setSmallForward($playerDoctrine->findOneBy(['playerId' => $data['smallForward']["playerId"]]));
        $userTeam->setPowerForward($playerDoctrine->findOneBy(['playerId' => $data['powerForward']["playerId"]]));
        $userTeam->setCenter($playerDoctrine->findOneBy(['playerId' => $data['center']["playerId"]]));

        return $userTeam;
    }

    public function pgNextAction(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $pg = $userRepository->getGuards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/pointGuards.html.twig', ['guards' => $pg, 'gCount' => $userRepository->allGuards]));
    }

    public function sgNextAction(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $sg = $userRepository->getGuards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/shootingGuards.html.twig', ['guards' => $sg, 'gCount' => $userRepository->allGuards]));
    }

    public function sfNextAction(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $sf = $userRepository->getForwards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/smallForwards.html.twig', ['forwards' => $sf, 'fCount' => $userRepository->allForwards]));
    }

    public function pfNextAction(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $pf = $userRepository->getForwards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/powerForwards.html.twig', ['forwards' => $pf, 'fCount' => $userRepository->allForwards]));
    }

    public function cNextAction(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $c = $userRepository->getCenters($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/centers.html.twig', ['centers' => $c, 'cCount' => $userRepository->allCenters]));
    }
}
