<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Entity\UserTeam;
use AppBundle\Form\UserTeamType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $user = $this->getUser();

        $userTeamRepository = $this->getDoctrine()->getRepository(UserTeam::class);
        $userRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);

        $guards = $userRepository->getGuards($user);
        $forwards = $userRepository->getForwards($user);
        $centers = $userRepository->getCenters($user);
        $userTeams = $userTeamRepository->findBy(['user' => $user]);

        $userTeam = new UserTeam();

        $form = $this->createFormBuilder($userTeam)
            ->add('name', TextType::class, array('label' => 'Name of team'))
            ->add('stadiumId', EntityType::class, array(
                'label' => 'Select Stadium',
                'class' => 'AppBundle:Stadium',
                'choice_label' => 'name',
            ))
            ->add('trainerId', EntityType::class, array(
                'label' => 'Select Trainer',
                'class' => 'AppBundle:Trainer',
                'choice_label' => 'fullName',
            ))
            ->add('save', SubmitType::class, array('label' => 'Create My Team'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $request->request->get('form');
            $userTeam = $form->getData();
            $this->setNewPlayers($userTeam, $playerDoctrine, $data);
            $userTeam->setUser($user);
            $userTeam->setLike(0);
            $userTeam->setDislike(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userTeam);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        $countTeam = count($userTeams);

        if($countTeam >= 3){
            die('ok');
        }

        return $this->render('starting5/dashboard/new.html.twig', array(
            'form' => $form->createView(),
            'userTeams' => $userTeams,
            'guards' => $guards,
            'forwards' => $forwards,
            'centers' => $centers,
        ));
    }

    public function editAction(Request $request, $id)
    {
        $userTeamRepository = $this->getDoctrine()->getRepository(UserTeam::class);
        $userTeam = $userTeamRepository->find($id);
        $form = $this->createForm(UserTeamType::class, $userTeam);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $userTeam = $form->getData();
            $em->persist($userTeam);
            $em->flush();

            return $this->redirectToRoute('user.team.edit', ['id' => $id]);
        }

        return $this->render('starting5/dashboard/teams/edit.html.twig', array(
            'form' => $form->createView(),
            'userTeam' => $userTeam,
            'id' => $id
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
        $userTeam->setPointGuard($playerDoctrine->findOneBy(['playerId' => $data['pointGuard']]));
        $userTeam->setShootingGuard($playerDoctrine->findOneBy(['playerId' => $data['shootingGuard']]));
        $userTeam->setSmallForward($playerDoctrine->findOneBy(['playerId' => $data['smallForward']]));
        $userTeam->setPowerForward($playerDoctrine->findOneBy(['playerId' => $data['powerForward']]));
        $userTeam->setCenter($playerDoctrine->findOneBy(['playerId' => $data['center']]));

        return $userTeam;
    }
}
