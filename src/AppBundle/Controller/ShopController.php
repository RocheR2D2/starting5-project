<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\Shop;
use AppBundle\Entity\UsersPlayers;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    private $em;
    protected $nbaPlayers;
    protected $userPlayers;
    protected $shopPlayer;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
        $this->nbaPlayers = $this->em->getRepository(NBAPlayers::class);
        $this->userPlayers = $this->em->getRepository(UsersPlayers::class);
        $this->shopPlayer = $this->em->getRepository(Shop::class);
    }

    public function shopPlayersAction()
    {
        $shopPlayers = $this->nbaPlayers->getShopPlayers();
        foreach ($shopPlayers as $shopPlayer) {
            $player = $shopPlayer["player"];
            $price = $shopPlayer["price"];
            $type = $shopPlayer["type"];
            $NBAPlayer = $this->nbaPlayers->find($player->id);
            $shop = new Shop();
            $shop->setPlayerId($NBAPlayer);
            $shop->setPrice($price);
            $shop->setType($type);
            $this->em->persist($shop);
            $this->em->flush();
        }

        echo "NEW PLAYERS IN SHOP"; die;
    }

    public function buyPlayersAction(Request $request)
    {
        $user = $this->getUser();
        $playerId = $request->request->get('playerId');
        $player = $this->nbaPlayers->find($playerId);
        $shopPlayer =  $this->shopPlayer->findOneBy(['playerId' => $player]);
        $this->addPlayer($player, $shopPlayer);
        $content = ['shopPlayer' => $this->shopPlayerCard($player, $shopPlayer), 'points' => $user->getQuizPoints(), 'lastPlayers' => $this->getLastPlayers()];
        $response = new Response(json_encode($content));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function getLastPlayers()
    {
        $lastPlayers = $this->userPlayers->findBy([], ['id' => 'DESC'], 5);

        return $this->renderView('starting5/players/lastPlayers.html.twig', ['lastPlayers' => $lastPlayers]);
    }

    public function shopPlayerCard($player, $shopPlayer)
    {
        $userPlayer = $this->userPlayers->findBy(['userId' => $this->getUser(), 'playerId' => $player]);
        if(!empty($userPlayer)) {
            $shopPlayer->isMyPlayer = empty($userPlayer);
        }

        return $this->renderView('starting5/players/shop.html.twig', [
            'player' => $shopPlayer->playerId,
            'price' => $shopPlayer->price,
            'isMyPlayer' => $shopPlayer->isMyPlayer,
        ]);
    }

    public function addPlayer($player, $shopPlayer)
    {
        $user = $this->getUser();
        $userPlayer = new UsersPlayers();
        $userPlayer->setPlayerId($player);
        $userPlayer->setUserId($user);
        $userPlayer->setPosition($player->getPosition());
        $userPlayer->setRating($player->getRating());

        $user->setQuizPoints($user->getQuizPoints() - $shopPlayer->price);

        $this->em->persist($userPlayer);
        $this->em->persist($user);
        $this->em->flush();
    }
}
