<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
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
        $user = $this->getUser();
        $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()]);
        $countPlayers = count($userPlayers) + 3;
        $silverForm = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'Silver Pack'))
            ->add('type', HiddenType::class, array(
                'data' => Pack::SILVER_PACK_LABEL,
            ))
            ->getForm();
        $goldenForm = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'Golden Pack'))
            ->add('type', HiddenType::class, array(
                'data' => Pack::GOLDEN_PACK_LABEL,
            ))
            ->getForm();
        $gigaForm = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'Giga Pack'))
            ->add('type', HiddenType::class, array(
                'data' => Pack::GIGA_PACK_LABEL,
            ))
            ->getForm();
        $superRareForm = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'Super Rare Pack'))
            ->add('type', HiddenType::class, array(
                'data' => Pack::SUPER_RARE_PACK_LABEL,
            ))
            ->getForm();

        $silverForm->handleRequest($request);
        $goldenForm->handleRequest($request);
        $gigaForm->handleRequest($request);
        $superRareForm->handleRequest($request);

        if ($silverForm->isSubmitted() || $goldenForm->isSubmitted() || $gigaForm->isSubmitted() || $superRareForm->isSubmitted()) {
            $hint = [];
            $userPlayers = $this->userPlayerRepository->findBy(['userId' => $this->getUser()->getId()]);
            $playersIds = [];
            foreach ($userPlayers as $userPlayer) {
                $playersIds[] = $userPlayer->getPlayerId()->getPlayerId();
            }
            $type = null;
            if($silverForm->isSubmitted()){
                if(isset($silverForm->getData()['type'])){
                    $type = $silverForm->getData()['type'];
                }
            } elseif($goldenForm->isSubmitted()){
                if(isset($goldenForm->getData()['type'])){
                    $type = $goldenForm->getData()['type'];
                }
            } elseif($gigaForm->isSubmitted()){
                if(isset($gigaForm->getData()['type'])){
                    $type = $gigaForm->getData()['type'];
                }
            } elseif($superRareForm->isSubmitted()){
                if(isset($superRareForm->getData()['type'])){
                    $type = $superRareForm->getData()['type'];
                }
            }
        }

        return $this->render('starting5/pack/index.html.twig', [
            'silverForm' => $silverForm->createView(),
            'goldenForm' => $goldenForm->createView(),
            'gigaForm' => $gigaForm->createView(),
            'superRareForm' => $superRareForm->createView(),
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

        $response = new Response($this->packContentView($packContent, $hint, $type));

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
