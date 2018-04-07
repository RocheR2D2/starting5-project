<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Helper\Pack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PackController extends Controller
{
    private $em;
    private $player;
    private $userPlayerRepository;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
        $this->player = $this->em->getRepository(NBAPlayers::class);
        $this->userPlayerRepository = $this->em->getRepository(UsersPlayers::class);
    }

    public function packOpeningAction(Request $request)
    {
        $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()]);
        $countPlayers = count($userPlayers) + 3;

        return $this->render('starting5/pack/index.html.twig', [
            'count' => $countPlayers
        ]);
    }

    public function packContentAction(Request $request) {
        $type = $request->request->get('type');
        $hint = [];
        $user = $this->getUser();

        $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()->getId()]);
        $playersIds = [];
        foreach ($userPlayers as $userPlayer) {
            $playersIds[] = $userPlayer->getPlayerId()->getPlayerId();
        }

        $packContent = $this->player->packOpener($type);

        foreach ($packContent as $content) {
            if($content && $user){
                if($type == Pack::GOLDEN_PACK_LABEL){
                    if($user->getQuizPoints() < Pack::GOLDEN_PACK_PRICE){
                        die('nope');
                    }
                    $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_GOLDEN_PLAYER); // 1500
                } elseif($type == Pack::SILVER_PACK_LABEL){
                    if($user->getQuizPoints() < Pack::SILVER_PACK_PRICE){
                        die('nope');
                    }
                    $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_SILVER_PLAYER); // 300
                } elseif($type == Pack::GIGA_PACK_LABEL){
                    if($user->getQuizPoints() < Pack::GIGA_PACK_PRICE){
                        die('nope');
                    }
                    $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_GIGA_PLAYER); // 3500
                } elseif($type == Pack::SUPER_RARE_PACK_LABEL){
                    if($user->getQuizPoints() < Pack::SUPER_RARE_PACK_PRICE){
                        die('nope');
                    }
                    $user->setQuizPoints($user->getQuizPoints() - Pack::UNIQ_SUPER_RARE_PLAYER); // 10500
                }
                $em = $this->getDoctrine()->getManager();
                $userPlayer = new UsersPlayers();
                $nbaPlayer = $this->player->findOneBy(['playerId' => $content->playerId]);
                if($nbaPlayer){
                    $playerId = $nbaPlayer->getPlayerId();
                    if(in_array($playerId, $playersIds)){
                        $user = $this->getUser();
                        $user->setQuizPoints($user->getQuizPoints() + Pack::UNIQ_REFUND_PLAYER);
                        continue;
                    }
                    $userPlayer->setPlayerId($nbaPlayer);
                    $userPlayer->setUserId($this->getUser());
                    $userPlayer->setPosition($nbaPlayer->getPosition());
                    $userPlayer->setRating($nbaPlayer->getRating());
                    if($nbaPlayer->getRating() > Pack::MINIMUM_RARE_RATING && $nbaPlayer->getRating() <= Pack::MAXIMUM_RARE_RATING){
                        $hint['rare'] = $nbaPlayer;
                    } else if ($nbaPlayer->getRating() > Pack::MAXIMUM_RARE_RATING && $nbaPlayer->getRating() <= Pack::MINIMUM_ULTRA_RARE_RATING) {
                        $hint['very_rare'] = $nbaPlayer;
                    } else if ($nbaPlayer->getRating() > Pack::MINIMUM_ULTRA_RARE_RATING && $nbaPlayer->getRating() <= Pack::MAXIMUM_ULTRA_RARE_RATING) {
                        $hint['ultra_rare'] = $nbaPlayer;
                    } else if ($nbaPlayer->getRating() >= Pack::EPIC_RATING) {
                        $hint['epic'] = $nbaPlayer;
                    }

                    $em->persist($user);
                    $em->persist($userPlayer);
                    $em->flush();
                }
            }
        }
        $responseContent['points'] = $user->getQuizPoints().' Pts';
        $responseContent['packContent'] = $this->packContentView($packContent, $hint, $type);

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function packContentView($packContent, $hint ,$type) {
        return $this->renderView('starting5/pack/pack.html.twig', [
            'packContent' => $packContent,
            'hint' => $hint,
            'type' => $type
        ]);
    }
}
