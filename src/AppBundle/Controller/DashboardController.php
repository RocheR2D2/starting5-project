<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\PublicTeam;
use AppBundle\Entity\Shop;
use AppBundle\Entity\Stadium;
use AppBundle\Entity\Trainer;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Entity\UserStadium;
use AppBundle\Entity\UserTeam;
use AppBundle\Entity\UserTrainer;
use AppBundle\Form\UserTeamType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DashboardController extends Controller
{
    private $em;
    protected $nbaPlayers;
    protected $playerRepository;
    protected $userTeamDoctrine;
    protected $userPlayers;
    protected $shopRepository;
    protected $stadiumRepository;
    protected $trainerRepository;
    protected $userTrainerRepository;
    protected $userStadiumRepository;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
        $this->nbaPlayers = $this->em->getRepository(NBAPlayers::class);
        $this->userTeamDoctrine = $this->em->getRepository(UserTeam::class);
        $this->userPlayers = $this->em->getRepository(UsersPlayers::class);
        $this->shopRepository = $this->em->getRepository(Shop::class);
        $this->stadiumRepository = $this->em->getRepository(Stadium::class);
        $this->trainerRepository = $this->em->getRepository(Trainer::class);
        $this->userStadiumRepository = $this->em->getRepository(UserStadium::class);
        $this->userTrainerRepository = $this->em->getRepository(UserTrainer::class);
    }

    public function homeAction() {
        $countMyPlayers = $this->userPlayers->countMyPlayers($this->getUser());
        $countAllPlayers = $this->nbaPlayers->countPlayers;

        $countMyStadiums = $this->userStadiumRepository->getMyStadium($this->getUser());
        $countAllStadiums = $this->stadiumRepository->countStadium;

        $countMyTrainers = $this->userTrainerRepository->getMyTrainer($this->getUser());
        $countAllTrainers = $this->trainerRepository->countTrainer;

        $lastPlayers = $this->userPlayers->findBy(['userId' => $this->getUser()], ['id' => 'DESC'], 5);
        $shopPlayers = $this->shopRepository->getShopPlayers($this->getUser());

        return $this->render('starting5/dashboard/home.html.twig',
            [
                'lastPlayers' => $lastPlayers,
                'shopPlayers' => $shopPlayers,
                'countMyPlayers' => $countMyPlayers,
                'countAllPlayers' => $countAllPlayers,
                'countMyStadiums' => $countMyStadiums,
                'countAllStadiums' => $countAllStadiums,
                'countMyTrainers' => $countMyTrainers,
                'countAllTrainers' => $countAllTrainers
            ]
        );
    }

    /**
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

            $pointGuard = $userTeam->pointGuard;
            $shootingGuard = $userTeam->shootingGuard;
            $smallForward = $userTeam->smallForward;
            $powerForward = $userTeam->powerForward;
            $center = $userTeam->center;

            $players = $this->setPlayers($players, $pointGuard, $shootingGuard, $smallForward, $powerForward, $center);
            $teamInfos = $this->setTeamData(
                $teamInfos,
                $userTeam->getName(),
                $userTeam->getTrainerId(),
                $userTeam->getStadiumId(),
                $userTeam->getLike(),
                $userTeam->getDislike(),
                $userTeam->getId(),
                $userTeam->getTeamRating(),
                $userTeam->getOffRating(),
                $userTeam->getDefRating());

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

    public function newAction()
    {
        $userTeam = $this->userTeamDoctrine->findOneBy(['user' => $this->getUser()]);

        return $this->render('starting5/dashboard/new.html.twig', ['userTeam' => $userTeam]);
    }

    public function getPlayerAction()
    {
        $user = $this->getUser();
        $myPlayers = $this->userPlayers->getMyPlayers($user);
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($myPlayers, 'json');
        $response = new Response($result);

        return $response;
    }


    public function createTeamAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $teamName = $data["teamName"];
        $players = $data["players"];
        $stadium = $data["stadium"];
        $trainer = $data["trainer"];

        $user = $this->getUser();
        $userTeam = new UserTeam();
        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $this->setNewPlayers($userTeam, $playerDoctrine, $players);
        $this->setTeamRating($userTeam, $players);
        $userTeam->setUser($user);
        $userTeam->setLike(0);
        $userTeam->setDislike(0);
        $userTeam->setActive(1);
        $userTeam->setName($teamName);
        $trainerRepo = $this->getDoctrine()->getRepository(Trainer::class);
        $trainer = $trainerRepo->find($trainer["id"]);

        $stadiumRepo = $this->getDoctrine()->getRepository(Stadium::class);
        $stadium = $stadiumRepo->find($stadium["id"]);

        $userTeam->setTrainerId($trainer);
        $userTeam->setStadiumId($stadium);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userTeam);
        $em->flush();

        return new Response("done");

    }

    public function editAction($id)
    {
        $userTeam = $this->userTeamDoctrine->find($id);

        return $this->render('starting5/dashboard/teams/edit.html.twig', array(
            'userTeam' => $userTeam
        ));
    }

    public function editTeamAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $id = $data["id"];

        $teamName = $data["teamName"];
        $players = $data["players"];
        $stadium = $data["stadium"];
        $trainer = $data["trainer"];

        $user = $this->getUser();

        $userTeamRepo = $this->getDoctrine()->getRepository(UserTeam::class);
        $userTeam = $userTeamRepo->find($id);

        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $this->setNewPlayers($userTeam, $playerDoctrine, $players);
        $this->setTeamRating($userTeam, $players);
        $userTeam->setUser($user);
        $userTeam->setLike(0);
        $userTeam->setDislike(0);
        $userTeam->setName($teamName);
        $trainerRepo = $this->getDoctrine()->getRepository(Trainer::class);
        $trainer = $trainerRepo->find($trainer["id"]);

        $stadiumRepo = $this->getDoctrine()->getRepository(Stadium::class);
        $stadium = $stadiumRepo->find($stadium["id"]);

        $userTeam->setTrainerId($trainer);
        $userTeam->setStadiumId($stadium);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userTeam);
        $em->flush();

        return new Response("done");

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

    public function setTeamData($teamInfos, $name, $trainer, $stadium, $like, $dislike, $user, $rating, $offRating, $defRating)
    {
        $teamInfos['name'] = $name;
        $teamInfos['trainer'] = $trainer;
        $teamInfos['stadium'] = $stadium;
        $teamInfos['like'] = $like;
        $teamInfos['dislike'] = $dislike;
        $teamInfos['id'] = $user;
        $teamInfos['teamRating'] = $rating;
        $teamInfos['offRating'] = $offRating;
        $teamInfos['defRating'] = $defRating;

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

    public function setTeamRating($userTeam, $players)
    {
        $userTeam->setTeamRating($this->userTeamDoctrine->getTeamRating($players));
        $userTeam->setOffRating($this->userTeamDoctrine->getOffRating($players));
        $userTeam->setDefRating($this->userTeamDoctrine->getDefRating($players));

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

    public function myTeamAction($id)
    {
        $userTeam = $this->userTeamDoctrine->find($id);
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($userTeam, 'json');
        $response = new Response($result);

        return $response;
    }

    public function publicFiveAction()
    {

        return $this->render('starting5/dashboard/public-team/public-five.html.twig');
    }

    public function getPublicPlayersAction()
    {
        $players = $this->nbaPlayers->findAll();
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($players, 'json');
        $response = new Response($result);

        return $response;
    }

    public function getPublicTrainersAction()
    {
        $players = $this->trainerRepository->findAll();
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($players, 'json');
        $response = new Response($result);

        return $response;
    }

    public function getPublicStadiumsAction()
    {
        $players = $this->stadiumRepository->findAll();
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($players, 'json');
        $response = new Response($result);

        return $response;
    }

    public function createPublicTeamAction(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        $teamName = $data["teamName"];
        $players = $data["players"];
        $stadium = $data["stadium"];
        $trainer = $data["trainer"];
        $username = $data["username"];

        $publicTeam = new PublicTeam();
        $playerDoctrine = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $this->setNewPlayers($publicTeam, $playerDoctrine, $players);
        $this->setTeamRating($publicTeam, $players);
        $publicTeam->setUsername($username);
        $publicTeam->setLike(0);
        $publicTeam->setDislike(0);
        $publicTeam->setName($teamName);
        $trainerRepo = $this->getDoctrine()->getRepository(Trainer::class);
        $trainer = $trainerRepo->find($trainer["id"]);

        $stadiumRepo = $this->getDoctrine()->getRepository(Stadium::class);
        $stadium = $stadiumRepo->find($stadium["id"]);

        $publicTeam->setTrainerId($trainer);
        $publicTeam->setStadiumId($stadium);

        $em = $this->getDoctrine()->getManager();
        $em->persist($publicTeam);
        $em->flush();

        return new Response("done");

    }
}
