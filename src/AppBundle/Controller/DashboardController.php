<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\Shop;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Entity\UserTeam;
use AppBundle\Form\UserTeamType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    private $em;
    protected $nbaPlayers;
    protected $playerRepository;
    protected $userTeamDoctrine;
    protected $userPlayers;
    protected $shopRepository;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
        $this->nbaPlayers = $this->em->getRepository(NBAPlayers::class);
        $this->userTeamDoctrine = $this->em->getRepository(UserTeam::class);
        $this->userPlayers = $this->em->getRepository(UsersPlayers::class);
        $this->shopRepository = $this->em->getRepository(Shop::class);
    }

    public function homeAction() {
        $lastPlayers = $this->userPlayers->findBy([], ['id' => 'DESC'], 5);
        $shopPlayers = $this->shopRepository->getShopPlayers($this->getUser());

        return $this->render('starting5/dashboard/home.html.twig',
            [
                'lastPlayers' => $lastPlayers,
                'shopPlayers' => $shopPlayers
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $userTeams = $this->userTeamDoctrine->findBy(['user' => $user]);
        $userTeamsDatas = new ArrayCollection();

        foreach ($userTeams as $userTeam) {
            $teams = new ArrayCollection();
            $players = new ArrayCollection();
            $teamInfos = new ArrayCollection();

            $pointGuard = $this->getPointGuard($userTeam, $this->nbaPlayers);
            $shootingGuard = $this->getShootingGuard($userTeam, $this->nbaPlayers);
            $smallForward = $this->getSmallForward($userTeam, $this->nbaPlayers);
            $powerForward = $this->getPowerForward($userTeam, $this->nbaPlayers);
            $center = $this->getCenter($userTeam, $this->nbaPlayers);

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

        $serializer = $this->container->get('serializer');

        $guards = $this->userPlayers->getGuards($user);
        $guardsJson = $serializer->serialize($guards, 'json');
        $gCount = $this->userPlayers->allGuards;
        $forwards = $this->userPlayers->getForwards($user);
        $forwardsJson = $serializer->serialize($forwards, 'json');
        $fCount = $this->userPlayers->allForwards;
        $centers = $this->userPlayers->getCenters($user);
        $centersJson = $serializer->serialize($centers, 'json');
        $cCount = $this->userPlayers->allCenters;
        $userTeams = $this->userTeamDoctrine->findBy(['user' => $user]);

        $pg = 'No Player Selected';
        $sg = 'No Player Selected';
        $sf = 'No Player Selected';
        $pf = 'No Player Selected';
        $c = 'No Player Selected';

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
            $this->setNewPlayers($userTeam, $this->nbaPlayers, $data);
            $userTeam->setUser($user);
            $userTeam->setLike(0);
            $userTeam->setDislike(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userTeam);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        $countTeam = count($userTeams);

        /*if($countTeam >= 3){
            die('ok');
        }*/

        return $this->render('starting5/dashboard/new.html.twig', array(
            'form' => $form->createView(),
            'guards' => $guards,
            'gCount' => $gCount,
            'forwards' => $forwards,
            'centers' => $centers,
            'fCount' => $fCount,
            'cCount' => $cCount,
            'guardsJson' => $guardsJson,
            'forwardsJson' => $forwardsJson,
            'centersJson' => $centersJson,
            'pg' => $pg,
            'sg' => $sg,
            'sf' => $sf,
            'pf' => $pf,
            'c' => $c,
        ));
    }

    public function editAction(Request $request, $id)
    {
        $user = $this->getUser();
        $userTeam = $this->userTeamDoctrine->find($id);
        $form = $this->createForm(UserTeamType::class, $userTeam);

        $pg = $this->userTeamDoctrine->find($id)->getPointGuard()->getFullName();
        $sg = $this->userTeamDoctrine->find($id)->getShootingGuard()->getFullName();
        $sf = $this->userTeamDoctrine->find($id)->getSmallForward()->getFullName();
        $pf = $this->userTeamDoctrine->find($id)->getPowerForward()->getFullName();
        $c = $this->userTeamDoctrine->find($id)->getCenter()->getFullName();

        $guards = $this->userPlayers->getGuards($user);
        $gCount = $this->userPlayers->allGuards;
        $forwards = $this->userPlayers->getForwards($user);
        $fCount = $this->userPlayers->allForwards;
        $centers = $this->userPlayers->getCenters($user);
        $cCount = $this->userPlayers->allCenters;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('form');
            $this->setNewPlayers($userTeam, $this->nbaPlayers, $data);
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
        $userTeam->setPointGuard($playerDoctrine->findOneBy(['playerId' => $data['pointGuard']]));
        $userTeam->setShootingGuard($playerDoctrine->findOneBy(['playerId' => $data['shootingGuard']]));
        $userTeam->setSmallForward($playerDoctrine->findOneBy(['playerId' => $data['smallForward']]));
        $userTeam->setPowerForward($playerDoctrine->findOneBy(['playerId' => $data['powerForward']]));
        $userTeam->setCenter($playerDoctrine->findOneBy(['playerId' => $data['center']]));

        return $userTeam;
    }

    public function pgNextAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $pg = $this->userPlayers->getGuards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/pointGuards.html.twig', ['guards' => $pg, 'gCount' => $this->userPlayers->allGuards]));
    }

    public function sgNextAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $sg = $this->userPlayers->getGuards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/shootingGuards.html.twig', ['guards' => $sg, 'gCount' => $this->userPlayers->allGuards]));
    }

    public function sfNextAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $sf = $this->userPlayers->getForwards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/smallForwards.html.twig', ['forwards' => $sf, 'fCount' => $this->userPlayers->allForwards]));
    }

    public function pfNextAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $pf = $this->userPlayers->getForwards($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/powerForwards.html.twig', ['forwards' => $pf, 'fCount' => $this->userPlayers->allForwards]));
    }

    public function cNextAction(Request $request)
    {
        $page = $data = $request->request->get('page');
        if($page < 0){
            $page = 0;
        }
        $c = $this->userPlayers->getCenters($this->getUser(), $page);

        return new Response($this->renderView('starting5/dashboard/positions/centers.html.twig', ['centers' => $c, 'cCount' => $this->userPlayers->allCenters]));
    }
}
