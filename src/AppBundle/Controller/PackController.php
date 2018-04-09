<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
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

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
        $this->player = $this->em->getRepository(NBAPlayers::class);
        $this->userPlayerRepository = $this->em->getRepository(UsersPlayers::class);
        $this->pack = new Pack();
    }

    /**
     * Pack View
     *
     * @return Response
     */
    public function packOpeningAction()
    {
        $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()]);
        /** @var integer $countPlayers */
        $countPlayers = count($userPlayers) + 3;
        /** @var array $packs */
        $packs = $this->pack->getPackList($this->getUser());

        return $this->render('starting5/pack/index.html.twig', [
            'count' => $countPlayers,
            'packs' => $packs
        ]);
    }

    /**
     * AJAX Post Pack Fill
     *
     * @param Request $request
     * @return Response
     */
    public function packContentAction(Request $request) {
        /** @var string $type */
        $type = $request->request->get('type');
        $user = $this->getUser();
        /** @var array $playersIds */
        $playersIds = $this->getMyPlayersIds();
        $packContent = $this->player->packOpener($type); // get Pack Content
        $this->fillPack($packContent, $user, $type, $playersIds);

        $response = $this->packResponse($user, $packContent, $type);

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
    public function fillPack($packContent, $user, $type, $playersIds) {
        foreach ($packContent as $content) {
            if($content && $user){
                $this->debitPoints($type, $user);
                $userPlayer = new UsersPlayers();
                $nbaPlayer = $this->player->findOneBy(['playerId' => $content->playerId]);
                if($nbaPlayer){
                    $this->addPlayerToUser($nbaPlayer, $playersIds, $userPlayer, $user);
                }
            }
        }
    }

    /**
     * Get array of users players ids
     *
     * @return array
     */
    public function getMyPlayersIds() {
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
    public function debitPoints($type, $user) {
        if($type == Pack::GOLDEN_PACK_LABEL){
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_GOLDEN_PLAYER); // 1500
        } elseif($type == Pack::SILVER_PACK_LABEL){
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_SILVER_PLAYER); // 300
        } elseif($type == Pack::GIGA_PACK_LABEL){
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_GIGA_PLAYER); // 3500
        } elseif($type == Pack::SUPER_RARE_PACK_LABEL){
            $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_SUPER_RARE_PLAYER); // 10500
        }
    }

    /**
     * Add Player to database
     *
     * @param $nbaPlayer
     * @param $playersIds
     * @param $userPlayer
     * @param $user
     */
    public function addPlayerToUser($nbaPlayer, $playersIds, $userPlayer, $user) {
        /** @var array $playerId */
        $playerId = $nbaPlayer->getPlayerId();
        if(in_array($playerId, $playersIds)){
            $user->setQuizPoints($user->getQuizPoints() + Pack::UNIQ_REFUND_PLAYER);
        } else {
            $userPlayer->setPlayerId($nbaPlayer);
            $userPlayer->setUserId($this->getUser());
            $userPlayer->setPosition($nbaPlayer->getPosition());
            $userPlayer->setRating($nbaPlayer->getRating());

            $this->em->persist($user);
            $this->em->persist($userPlayer);
            $this->em->flush();
        }
    }

    /**
     * Create JSON array for AJAX with Content view, points and Pack List
     *
     * @param $user
     * @param $packContent
     * @param $type
     * @return Response
     */
    public function packResponse($user, $packContent, $type) {
        /** @var array $responseContent */
        $responseContent = [];

        $responseContent['points'] = $user->getQuizPoints();
        $responseContent['packContent'] = $this->packContentView($packContent, $type);
        $responseContent['packList'] = $this->packList();

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
    public function packContentView($packContent, $type) {
        return $this->renderView('starting5/pack/pack.html.twig', [
            'packContent' => $packContent,
            'type' => $type
        ]);
    }

    /**
     * Render pack List
     *
     * @return string
     */
    public function packList() {
        /** @var array $packs */
        $packs = $this->pack->getPackList($this->getUser());

        return $this->renderView('starting5/pack/packs.html.twig', [
            'packs' => $packs
        ]);
    }
}
