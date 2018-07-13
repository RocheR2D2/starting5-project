<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
use AppBundle\Entity\BattlePlayers;
use AppBundle\Entity\BattlePlays;
use AppBundle\Entity\BattleRound;
use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\PlayType;
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
    private $playType;
    private $battlePlay;
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
        $this->playType = $this->em->getRepository(PlayType::class);
        $this->battlePlay = $this->em->getRepository(BattlePlays::class);
        $this->attackDefenseRound = $this->playType->getAttackDefenseRound();
        $this->defenseAttackRound = $this->playType->getDefenseAttackRound();
    }

    public function isAuthorized($battleId)
    {
        $isBattlePlayerOne = $this->battle->findOneBy(['id' => $battleId, 'playerOneId' => $this->getUser()]);
        $isBattlePlayerTwo = $this->battle->findOneBy(['id' => $battleId, 'playerTwoId' => $this->getUser()]);

        if(!$isBattlePlayerOne && !$isBattlePlayerTwo) {
            return $this->render('starting5/404.html.twig');
        }
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
        $homeActiveBattle = $this->battle->findBy(['playerOneId' => $this->getUser(), 'isAccepted' => true, 'isDeclined' => false, 'active' => true], ['id' => 'DESC'], 3);
        $homeOverBattle = $this->battle->findBy(['playerOneId' => $this->getUser(), 'isAccepted' => true, 'isDeclined' => false, 'active' => false], ['id' => 'DESC'], 3);
        $homeWaitingBattle = $this->battle->findBy(['playerOneId' => $this->getUser(), 'isAccepted' => false, 'isDeclined' => NULL, 'isWaiting' => true,'active' => false], ['id' => 'DESC'], 3);

        $awayActiveBattle = $this->battle->findBy(['playerTwoId' => $this->getUser(), 'isAccepted' => true, 'isDeclined' => false, 'active' => true], ['id' => 'DESC'], 3);
        $awayOverBattle = $this->battle->findBy(['playerTwoId' => $this->getUser(), 'isAccepted' => true, 'isDeclined' => false, 'active' => false], ['id' => 'DESC'], 3);
        $awayInvitationBattle = $this->battle->findBy(['playerTwoId' => $this->getUser(), 'isAccepted' => false, 'isDeclined' => NULL, 'isWaiting' => true, 'active' => false], ['id' => 'DESC'], 3);

        return $this->render('starting5/battle/list.html.twig', [
            'homeOverBattle' => $homeOverBattle,
            'homeActiveBattle' => $homeActiveBattle,
            'homeWaitingBattle' => $homeWaitingBattle,
            'awayActiveBattle' => $awayActiveBattle,
            'awayInvitationBattle' => $awayInvitationBattle,
            'awayOverBattle' => $awayOverBattle
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

        $activeTeam = $this->userTeamDoctrine->findOneBy(['user' => $playerOne, 'active' => 1]);
        $battle = $this->setNewBattle($activeTeam, $opponentTeam);

        $this->em->persist($battle);
        $this->em->flush();

        $route = $this->generateUrl('battle.list');
        $response = new Response(json_encode($route));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function acceptAction(Request $request)
    {
        $battleId = $request->get('battleId');
        $battle = $this->battle->find($battleId);

        $opponentTeam = $this->userTeamDoctrine->findOneBy(['user' => $battle->getPlayerTwoId(), 'active' => 1]);
        $activeTeam = $this->userTeamDoctrine->findOneBy(['user' => $battle->getPlayerOneId(), 'active' => 1]);

        $this->setBattlePlayers($opponentTeam, $battle);
        $this->setBattlePlayers($activeTeam, $battle);
        $this->setBattleRound($battle, $battle->getPlayerOneId(), $battle->getPlayerTwoId());

        $battle
            ->setIsAccepted(true)
            ->setIsDeclined(false)
            ->setIsWaiting(false)
            ->setActive(true);

        $this->em->persist($battle);
        $this->em->flush();

        die('Challenge accepted');
    }

    public function declineAction(Request $request)
    {
        $battleId = $request->get('battleId');
        $battle = $this->battle->find($battleId);

        $battle
            ->setIsAccepted(false)
            ->setIsDeclined(true)
            ->setActive(false);

        $this->em->persist($battle);
        $this->em->flush();

        die('Challenge declined');
    }

    public function detailAction($battleId)
    {
        $this->isAuthorized($battleId);

        $battle = $this->battle->find($battleId);
        $playerOnePlayers = $this->battlePlayers->findBy(['battleId' => $battle, 'userId' => $this->getUser()]);
        $rounds = $this->battleRound->findBy(['battleId' => $battle], ['round' => 'ASC']);
        $activeRound = $this->battleRound->getActiveRound($battle);
        $playerBattleRound = $this->battleRound->findOneBy(['round' => $activeRound, 'attackerId' => $this->getUser(), 'battleId' => $battleId]);
        if(empty($playerBattleRound)) {
            $playerBattleRound = $this->battleRound->findOneBy(['round' => $activeRound, 'defenderId' => $this->getUser(),'battleId' => $battleId]);
        }
        $isPlayMade = $this->battlePlay->findBy(['userId' => $this->getUser(), 'battleRoundId' => $playerBattleRound]);

        return $this->render('starting5/battle/detail.html.twig', [
            'battle' => $battle,
            'rounds' => $rounds,
            'players' => $playerOnePlayers,
            'activeRound'=> $activeRound,
            'playMade' => $isPlayMade
        ]);
    }

    public function setNewBattle($activeTeam, $opponentTeam)
    {
        $battle = new Battle();
        $battle
            ->setActive(false)
            ->setIsAccepted(false)
            ->setPlayerOneScore(0)
            ->setPlayerTwoScore(0)
            ->setIsWaiting(true)
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
        foreach ($this->attackDefenseRound as $round => $playType) {
            $this->createBattleRound($battle, $playerOne, $playerTwo, $round, $playType);
        }

        foreach ($this->defenseAttackRound as $round => $playType) {
            $this->createBattleRound($battle, $playerTwo, $playerOne, $round, $playType);
        }
    }

    public function createBattleRound($battle, $attacker, $defender, $round, $playType)
    {
        $battleRound = new BattleRound();
        $battleRound
            ->setBattleId($battle)
            ->setRound($round)
            ->setAttackerId($attacker)
            ->setDefenderId($defender)
            ->setPlayType($playType);

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
