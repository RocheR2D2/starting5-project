<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
use AppBundle\Entity\BattlePlayers;
use AppBundle\Entity\BattleRound;
use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UserTeam;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BattleController extends Controller
{
    private $userTeamDoctrine;
    private $em;
    private $nbaPlayer;
    private $battle;
    private $battleRound;
    private $battlePlayers;
    private $attackDefenseRound = [];
    private $defenseAttackRound = [];

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
        $this->userTeamDoctrine = $this->em->getRepository(UserTeam::class);
        $this->nbaPlayer = $this->em->getRepository(NBAPlayers::class);
        $this->battle = $this->em->getRepository(Battle::class);
        $this->battleRound = $this->em->getRepository(BattleRound::class);
        $this->battlePlayers = $this->em->getRepository(BattlePlayers::class);
        $this->attackDefenseRound = [1, 3];
        $this->defenseAttackRound = [2, 4];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $activeTeam = $this->userTeamDoctrine->findOneBy(['user' => $this->getUser(), 'active' => 1]);

        return $this->render('starting5/battle/index.html.twig', [
            'name' => "Starting 5 - BATTLEMODE",
            'activeTeam' => $activeTeam
        ]);
    }

    public function listAction()
    {
        $homeBattles = $this->battle->findBy(['playerOneId' => $this->getUser()], ['active' => 'DESC']);
        $awayBattles = $this->battle->findBy(['playerTwoId' => $this->getUser()], ['active' => 'DESC']);
        return $this->render('starting5/battle/list.html.twig', [
            'homeBattles' => $homeBattles,
            'awayBattles' => $awayBattles
        ]);
    }

    public function searchAction()
    {
        $activeTeam = $this->userTeamDoctrine->findOneBy(['user' => $this->getUser(), 'active' => 1]);
        $activeTeamRating = $activeTeam->getTeamRating();
        $activeTeamId = $activeTeam->getId();
        $minTeamRatingOpponentTeam = $activeTeamRating - 5;
        $maxTeamRatingOpponentTeam = $activeTeamRating + 5;

        $opponents = $this->userTeamDoctrine->searchPlayer($minTeamRatingOpponentTeam, $maxTeamRatingOpponentTeam, $activeTeamId);
        if (!empty($opponents)) {
            $opponentKey = array_rand($opponents);
            $opponent = $opponents[$opponentKey];
            $user = $opponent->getUser();

            return $this->opponentResponse($opponent, $user);
        }

        return $this->opponentResponse();
    }

    public function confrontAction(Request $request)
    {
        $playerOne = $this->getUser();

        $opponentTeamId = $request->get('opponentTeamId');
        $opponentTeam = $this->userTeamDoctrine->findOneBy(['id' => $opponentTeamId]);

        $playerTwo = $opponentTeam->getUser();

        $activeTeam = $this->userTeamDoctrine->findOneBy(['user' => $playerOne, 'active' => 1]);
        $battle = $this->setNewBattle($activeTeam, $opponentTeam);

        $this->em->persist($battle);
        $this->em->flush();

        $this->setBattlePlayers($opponentTeam, $battle);
        $this->setBattlePlayers($activeTeam, $battle);
        $this->setBattleRound($battle, $playerOne, $playerTwo);

        $route = $this->generateUrl('battle.list');
        $response = new Response(json_encode($route));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function detailAction($battleId)
    {
        $battle = $this->battle->find($battleId);
        $playerOnePlayers = $this->battlePlayers->findBy(['battleId' => $battle, 'userId' => $this->getUser()]);
        $rounds = $this->battleRound->findBy(['battleId' => $battle], ['round' => 'ASC']);
        return $this->render('starting5/battle/detail.html.twig', [
            'battle' => $battle,
            'rounds' => $rounds,
            'players' => $playerOnePlayers
        ]);
    }

    public function setNewBattle($activeTeam, $opponentTeam)
    {
        $battle = new Battle();
        $battle
            ->setActive(true)
            ->setPlayerOneScore(0)
            ->setPlayerTwoScore(0)
            ->setPlayerOneId($activeTeam->getUser())
            ->setPlayerTwoId($opponentTeam->getUser());

        return $battle;
    }

    /**
     * round 1 - playerOne ATT - PlayerTwo DEF
     * round 2 - playerTwo ATT - PlayerOne DEF
     * round 3 - playerOne ATT - PlayerTwo DEF
     * round 4 - playerTwo ATT - PlayerOne DEF
     *
     * @param $battle
     * @param $playerOne
     * @param $playerTwo
     */
    public function setBattleRound($battle, $playerOne, $playerTwo)
    {
        foreach ($this->attackDefenseRound as $round) {
            $this->createBattleRound($battle, $playerOne, $playerTwo, $round);
        }

        foreach ($this->defenseAttackRound as $round) {
            $this->createBattleRound($battle, $playerTwo, $playerOne, $round);
        }
    }

    public function createBattleRound($battle, $attacker, $defender, $round)
    {
        $battleRound = new BattleRound();
        $battleRound
            ->setBattleId($battle)
            ->setRound($round)
            ->setAttackerId($attacker)
            ->setDefenderId($defender);

        $this->em->persist($battleRound);
        $this->em->flush();
    }

    public function setBattlePlayers($team, $battle)
    {
        foreach ($team as $nbaPlayer) {
            $battlePlayers = new BattlePlayers();
            $player = $this->nbaPlayer->findOneBy(['playerId' => $nbaPlayer->getPlayerId()]);
            $battlePlayers
                ->setUserId($team->getUser())
                ->setPlayerId($player)
                ->setActionPoint(2)
                ->setPosition($nbaPlayer->getPosition())
                ->setRating($nbaPlayer->getRating())
                ->setBattleId($battle);

            $this->em->persist($battlePlayers);
            $this->em->flush();
        }
    }

    public function opponentResponse($opponent = null, $user = null)
    {
        $view = $this->noMatchView();
        $arrayResponse = [];
        $arrayResponse['button'] = null;
        if ($opponent && $user) {
            $view = $this->opponentView($opponent, $user);
            $arrayResponse['button'] = $this->confrontButtonView($opponent, $user);
        }
        $arrayResponse['opponent'] = $view;
        $response = new Response(json_encode($arrayResponse));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function opponentView($opponent, $user)
    {
        return $this->renderView('starting5/battle/opponent.html.twig', [
            'team' => $opponent,
            'user' => $user
        ]);
    }

    public function noMatchView()
    {
        return $this->renderView('starting5/battle/noOpponent.html.twig');
    }

    public function confrontButtonView($opponent, $user)
    {
        return $this->renderView('starting5/battle/confrontButton.html.twig', [
            'team' => $opponent,
            'user' => $user
        ]);
    }
}
