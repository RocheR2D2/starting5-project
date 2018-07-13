<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\Stadium;
use AppBundle\Entity\Trainer;
use AppBundle\Entity\User;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Entity\UserStadium;
use AppBundle\Entity\UserTrainer;
use AppBundle\Helper\Player;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Helper\Pack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PackController
 * @package AppBundle\Controller
 */
class PackController extends Controller
{
    private $em;
    private $player;
    private $userPlayerRepository;
    protected $pack;
    private $duplicatePlayers;
    public $refund;
    private $playerHelper;
    private $playerMapping;
    private $trainerRepository;
    private $stadiumRepository;
    private $userStadiumRepository;
    private $userTrainerRepository;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
        $this->player = $this->em->getRepository(NBAPlayers::class);
        $this->userPlayerRepository = $this->em->getRepository(UsersPlayers::class);
        $this->trainerRepository = $this->em->getRepository(Trainer::class);
        $this->stadiumRepository = $this->em->getRepository(Stadium::class);
        $this->userStadiumRepository = $this->em->getRepository(UserStadium::class);
        $this->userTrainerRepository = $this->em->getRepository(UserTrainer::class);
        $this->playerHelper = new Player();
        $this->pack = new Pack();
        $this->duplicatePlayers = [];
        $this->playerMapping = $this->playerHelper->mapping;

    }

    /**
     * Pack View
     *
     * @return Response
     */
    public function packOpeningAction()
    {
        $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()]);

        $UserRepo = $this->em->getRepository(User::class);
        $user = $UserRepo->find($this->getUser());

        $points = $user->getQuizPoints();

        /** @var integer $countPlayers */
        $countPlayers = count($userPlayers) + 3;
        /** @var array $packs */
        $packs = $this->pack->getPackList($this->getUser());

        return $this->render('starting5/pack/index.html.twig', [
            'count' => $countPlayers,
            'packs' => $packs,
            'points' => $points
        ]);
    }

    /**
     * AJAX Post Pack Fill
     *
     * @param Request $request
     * @return Response
     */
    public function packContentAction(Request $request)
    {
        /** @var string $type */
        $type = $request->request->get('type');
        $user = $this->getUser();
        /** @var array $playersIds */
        $packContent = $this->player->packOpener($type); // get Pack Content
        $trainer = $this->trainerRepository->getRandomTrainer();
        $stadium = $this->stadiumRepository->getRandomStadium();
        $this->fillPack($packContent, $user, $type, $trainer, $stadium);
        $this->countPackOpening($user);

        $response = $this->packResponse($user, $packContent, $type, $this->duplicatePlayers, $trainer, $stadium);

        return $response;
    }

    /**
     * Debit Points and add Player to Database
     *
     * @param $packContent
     * @param $user
     * @param $type
     * @param $playersIds
     */
    public function fillPack($packContent, $user, $type, $trainer, $stadium)
    {
        $this->addUserStadium($user, $stadium);
        $this->addUserTrainer($user, $trainer);
        foreach ($packContent as $content) {
            if (isset($content['player']) && isset($content['level']) && $user) {
                $this->getRefundPlayers($content['player'], $content['level']);
                $this->debitPoints($type, $user);
                $userPlayer = new UsersPlayers();
                $nbaPlayer = $this->player->findOneBy(['playerId' => $content['player']->playerId]);
                if ($nbaPlayer) {
                    /** @var array $playerId */
                    $playerId = $nbaPlayer->getPlayerId();
                    if(in_array($playerId, $this->getMyPlayersIds())){
                        $user->setQuizPoints($user->getQuizPoints() + $this->playerMapping[$content['level']]);
                        $this->em->persist($user);
                        $this->em->flush();
                    } else {
                        $this->addPlayerToUser($nbaPlayer, $userPlayer, $user);
                    }
                }
            }
        }
    }

    public function countPackOpening($user)
    {
        $openPack = $user->getOpenPack();
        $user->setOpenPack($openPack + 1);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function addUserStadium($user, $stadium)
    {
        if($stadium && $this->userStadiumRepository->checkStadium($user, $stadium)) {
            $userStadium = new UserStadium();
            $userStadium->setUserId($user);
            $userStadium->setStadiumId($stadium);

            $this->em->persist($userStadium);
            $this->em->flush();
        }
    }

    public function addUserTrainer($user, $trainer)
    {
        if($trainer && $this->userTrainerRepository->checkTrainer($user, $trainer)) {
            $userTrainer = new UserTrainer();
            $userTrainer->setUserId($user);
            $userTrainer->setTrainerId($trainer);

            $this->em->persist($userTrainer);
            $this->em->flush();
        }
    }

    /**
     * Get array of users players ids
     *
     * @return array
     */
    public function getMyPlayersIds()
    {
        /** @var array $playersIds */
        $playersIds = [];
        $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()->getId()]);
        foreach ($userPlayers as $userPlayer) {
            $playersIds[] = $userPlayer->getPlayerId()->getPlayerId();
        }

        return $playersIds;
    }

    /**
     * Debit points from pack opening
     *
     * @param $type
     * @param $user
     */
    public function debitPoints($type, $user)
    {
        if ($type == Pack::GOLDEN_PACK_LABEL) {
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_GOLDEN_PLAYER); // 1500
        } elseif ($type == Pack::SILVER_PACK_LABEL) {
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_SILVER_PLAYER); // 300
        } elseif ($type == Pack::GIGA_PACK_LABEL) {
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_GIGA_PLAYER); // 3500
        } elseif ($type == Pack::SUPER_RARE_PACK_LABEL) {
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_SUPER_RARE_PLAYER); // 10500
        }
    }

    private function getRefundPlayers($player, $level)
    {
        if (in_array($player->playerId, $this->getMyPlayersIds())) {
            $this->duplicatePlayers[] = ['player' => $player, 'refund' => $this->playerMapping[$level]];
        };
    }

    /**
     * Add Player to database
     *
     * @param $nbaPlayer
     * @param $playersIds
     * @param $userPlayer
     * @param $user
     * @param $level
     */
    public function addPlayerToUser($nbaPlayer, $userPlayer, $user)
    {
        $userPlayer->setPlayerId($nbaPlayer);
        $userPlayer->setUserId($this->getUser());
        $userPlayer->setPosition($nbaPlayer->getPosition());
        $userPlayer->setRating($nbaPlayer->getRating());

        $this->em->persist($user);
        $this->em->persist($userPlayer);
        $this->em->flush();
    }

    /**
     * Create JSON array for AJAX with Content view, points and Pack List
     *
     * @param $user
     * @param $packContent
     * @param $type
     * @return Response
     */
    public function packResponse($user, $packContent, $type, $duplicatePlayers, $trainer, $stadium)
    {
        /** @var array $responseContent */
        $responseContent = [];
        $responseContent['points'] = $user->getQuizPoints();
        $responseContent['packContent'] = $this->packContentView($packContent, $type, ['trainer' => $trainer, 'stadium' => $stadium]);
        $responseContent['packList'] = $this->packList();
        $responseContent['duplicatePlayers'] = $this->duplicateView($duplicatePlayers);
        $responseContent['trainer'] = $trainer;
        $responseContent['stadium'] = $stadium;

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Render pack Content view
     *
     * @param $packContent
     * @param $type
     * @return string
     */
    public function packContentView($packContent, $type, $trainerStadium)
    {
        return $this->renderView('starting5/pack/pack.html.twig', [
            'packContent' => $packContent,
            'type' => $type,
            'trainerStadium' => $trainerStadium
        ]);
    }

    public function duplicateView($duplicatePlayers)
    {
        return $this->renderView('starting5/pack/duplicate.html.twig', [
            'duplicatePlayers' => $duplicatePlayers,
        ]);
    }

    /**
     * Render pack List
     *
     * @return string
     */
    public function packList()
    {
        /** @var array $packs */
        $packs = $this->pack->getPackList($this->getUser());

        return $this->renderView('starting5/pack/packs.html.twig', [
            'packs' => $packs
        ]);
    }
}
