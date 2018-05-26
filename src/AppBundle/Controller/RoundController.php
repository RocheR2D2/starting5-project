<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
use AppBundle\Entity\BattlePlayers;
use AppBundle\Entity\BattlePlays;
use AppBundle\Entity\BattleRound;
use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\PlayType;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RoundController extends Controller
{
    private $em;
    private $battle;
    private $battleRound;
    private $battlePlayers;
    private $battlePlays;
    private $NBAPlayers;
    private $playType;
    private $playTypeMapping;
    private $user;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
        $this->battleRound = $this->em->getRepository(BattleRound::class);
        $this->battle = $this->em->getRepository(Battle::class);
        $this->battlePlayers = $this->em->getRepository(BattlePlayers::class);
        $this->battlePlays = $this->em->getRepository(BattlePlays::class);
        $this->NBAPlayers = $this->em->getRepository(NBAPlayers::class);
        $this->playType = $this->em->getRepository(PlayType::class);
        $this->playTypeMapping = $this->playType->playTypeMapping();
        $this->user = $this->em->getRepository(User::class);
    }

    public function isAuthorized($battleId)
    {
        $isBattleRoundPlayerOne = $this->battleRound->findOneBy(['battleId' => $battleId, 'attackerId' => $this->getUser()]);
        $isBattleRoundPlayerTwo = $this->battleRound->findOneBy(['battleId' => $battleId, 'defenderId' => $this->getUser()]);

        if (!$isBattleRoundPlayerOne || !$isBattleRoundPlayerTwo) {
            throw $this->createNotFoundException();
        }
    }

    public function isPlayed($roundId, $user)
    {
        $play = $this->battlePlays->findBy(['battleRoundId' => $roundId, 'userId' => $user]);
        if (!empty($play)) {

            return true;
        }

        return false;
    }

    public function hasPlayersPlayed($battleId, $roundId)
    {
        $battle = $this->battle->find($battleId);
        $battleRound = $this->battleRound->find($roundId);
        $battleRoundType = $battleRound->getPlayType();
        $attackerId = $battleRound->getAttackerId();
        $defenderId = $battleRound->getDefenderId();
        $playerOneId = $battle->getPlayerOneId();

        $playerByAttacker = $this->battlePlays->findBy(['battleRoundId' => $roundId, 'userId' => $attackerId]);
        $playerByDefender = $this->battlePlays->findBy(['battleRoundId' => $roundId, 'userId' => $defenderId]);

        if (!empty($playerByDefender) && !empty($playerByAttacker)
            && count($playerByAttacker) == $battleRoundType->getId() && count($playerByDefender) == $battleRoundType->getId()
            && count($playerByAttacker) == count($playerByDefender)) {
            if (!$battleRound->isDone()) {
                $attackerScore = [];
                $defenderScore = [];
                foreach ($playerByAttacker as $key => $player) {
                    if (isset($playerByDefender[$key])) {
                        $playerTwo = $playerByDefender[$key];
                        $ratingAttacker = $player->getRating();
                        $ratingDefender = $playerTwo->getRating();
                        if($ratingAttacker > $ratingDefender) {
                            $player->setPointsMade(1);
                            $battleRound->setAttackerPoints($battleRound->getAttackerPoints() + 1);

                            $this->em->persist($player);
                            $this->em->persist($battleRound);
                            $this->em->flush();
                            $attackerScore[] = 1;
                        } elseif ($ratingAttacker < $ratingDefender) {
                            $playerTwo->setPointsMade(1);
                            $playerTwo->setSuccessfullDefense(true);
                            $battleRound->setDefenderPoints($battleRound->getDefenderPoints() + 1);

                            $this->em->persist($playerTwo);
                            $this->em->persist($battleRound);
                            $this->em->flush();
                            $defenderScore[] = 1;
                        }
                    }
                }

                if($attackerId == $playerOneId) {
                    $playerOneScore = array_sum($attackerScore);
                    $playerTwoScore = array_sum($defenderScore);
                } else {
                    $playerOneScore = array_sum($defenderScore);
                    $playerTwoScore = array_sum($attackerScore);
                }

                $battleRound->setDone(true);

                $battle->setPlayerOneScore($battle->getPlayerOneScore() + $playerOneScore);
                $battle->setPlayerTwoScore($battle->getPlayerTwoScore() + $playerTwoScore);

                $this->em->persist($battleRound);
                $this->em->persist($battle);

                $this->em->flush();

                if ($battleRound->getRound() > 3) {
                    if ($battle->getPlayerOneScore() != $battle->getPlayerTwoScore()) {
                        $winnerUserId = $playerOneId;
                        if ($playerTwoScore > $playerOneScore) {
                            $winnerUserId = $playerOneId;
                        }
                        $battle->setActive(0);
                        $battle->setWinnerUserId($winnerUserId);
                        $this->em->flush();
                    } else {
                        $battle->setActive(0);
                        $battle->setWinnerUserId(0);
                        $this->em->flush();
                    }
                }
            }
        }
    }

    /**
     * @param $battleId
     * @param $roundId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($battleId, $roundId)
    {
        $this->isAuthorized($battleId);
        $this->hasPlayersPlayed($battleId, $roundId);
        $user = $this->getUser();

        $isPlayed = $this->isPlayed($roundId, $user, $battleId);
        if ($isPlayed) {
            $round = $this->battleRound->find($roundId);
            $battle = $this->battle->find($battleId);
            $battleRoundType = $round->getPlayType();
            $attacker = $round->getAttackerId();
            $defender = $round->getDefenderId();

            $offPlayers = $this->battlePlays->findBy(['userId' => $attacker, 'battleRoundId' => $roundId]);
            $defPlayers = $this->battlePlays->findBy(['userId' => $defender, 'battleRoundId' => $roundId]);
            $roundResult = [];

            if (!empty($defPlayers) && !empty($offPlayers)
                && count($offPlayers) == $battleRoundType->getId() && count($defPlayers) == $battleRoundType->getId()
                && count($offPlayers) == count($defPlayers)) {

                $roundResult['score'] = $attacker->getUsername() .' '. $round->getAttackerPoints() . ' - ' . $round->getDefenderPoints() . ' ' .$defender->getUsername() ;
                $roundResult['battleScore'] = $battle->getPlayerTwoId()->getUsername() .' '. $battle->getPlayerTwoScore(). ' - ' .$battle->getPlayerOneScore() . ' ' . $battle->getPlayerOneId()->getUsername() ;
            }

            return $this->render('starting5/battle/round/played.html.twig', [
                'attacker' => $attacker,
                'defender' => $defender,
                'offPlayers' => $offPlayers,
                'defPlayers' => $defPlayers,
                'roundResult' => $roundResult,
                'battleId' => $battleId
            ]);
        }

        $play = $this->battleRound->findOneBy(['battleId' => $battleId, 'id' => $roundId]);
        $playerType = $this->battleRound->battleTypeLabel($battleId, $roundId, $user);
        $playType = $play->getPlayType();

        if (isset($this->playTypeMapping[$playType->getId()])) {
            $playTypeTemplate = 'starting5/battle/round/type/' . $this->playTypeMapping[$playType->getId()];

            $battlePlayers = $this->battlePlayers->findBy(['userId' => $this->getUser(), 'battleId' => $battleId]);

            return $this->render($playTypeTemplate, [
                'playerType' => $playerType,
                'battlePlayers' => $battlePlayers,
                'battleId' => $battleId,
                'roundId' => $roundId,
            ]);
        }

        return null;
    }

    public function createPlayAction(Request $request)
    {
        $players = $request->request->all();
        $players = $this->createPlayersData($players);
        $user = $this->getUser();
        $battleRoundId = $request->request->get('roundId');
        $battleId = $request->request->get('battleId');
        $battleRound = $this->battleRound->find($battleRoundId);
        $isAttacker = $this->battleRound->isAttacker($battleId, $battleRoundId, $user);

        $criticalPlayer = $this->NBAPlayers->find($request->request->get('isCritical'));

        foreach ($players as $key => $data) {
            $isCritical = false;
            if($data == $criticalPlayer) {
                $isCritical = true;
            }

            $bonus = rand(-5, 5);

            $playerOffRating = $data->getOffensiveRating();
            $playerDefRating = $data->getDefensiveRating();

            $rating = $playerDefRating + $bonus;

            if($isAttacker) {
                $rating = $playerOffRating + $bonus;
            }

            $battlePlayer = $this->battlePlayers->findOneBy(['playerId' => $data, 'userId' => $user, 'battleId' => $battleId]);
            $battlePlayer->setActionPoint($battlePlayer->getActionPoint() - 1);

            $battlePlay = new BattlePlays();
            $battlePlay
                ->setIsAttacker($isAttacker)
                ->setBonus($bonus)
                ->setPosition($key)
                ->setPlayerId($data)
                ->setUserId($user)
                ->setBattleRoundId($battleRound)
                ->setIsCritical($isCritical)
                ->setBattlePlayerId($battlePlayer)
                ->setRating($rating);

            $this->em->persist($battlePlay);
            $this->em->persist($battlePlayer);
            $this->em->flush();
        }
    }

    public function createPlayersData($players)
    {
        $NBAPlayers = [];

        if (isset($players['playType']) && isset($players['battleId']) && isset($players['isCritical']) && isset($players['roundId'])) {
            unset($players['playType']);
            unset($players['battleId']);
            unset($players['roundId']);
            unset($players['isCritical']);
        }

        foreach ($players as $player) {
            $NBAPlayers[] = $this->NBAPlayers->findOneBy(['playerId' => $player]);
        }

        return $NBAPlayers;
    }
}