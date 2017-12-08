<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class PackController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function packOpeningAction(Request $request)
    {
        $player = $this->getDoctrine()->getRepository(NBAPlayers::class);
        $userPlayerRepository = $this->getDoctrine()->getRepository(UsersPlayers::class);
        $userPlayers = $userPlayerRepository->findBy(['userId' => $this->getUser()]);
        $countPlayers = count($userPlayers) + 3;
        $silverForm = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'Silver Pack'))
            ->add('type', HiddenType::class, array(
                'data' => 'silver',
            ))
            ->getForm();
        $goldenForm = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'Golden Pack'))
            ->add('type', HiddenType::class, array(
                'data' => 'golden',
            ))
            ->getForm();

        $silverForm->handleRequest($request);
        $goldenForm->handleRequest($request);

        if ($silverForm->isSubmitted() || $goldenForm->isSubmitted()) {
            $hint = [];
            $userPlayers = $userPlayerRepository->findBy(['userId' => $this->getUser()->getId()]);
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
            }
            $packContent = $player->packOpener($type);

            foreach ($packContent as $content) {
                $user = $this->getUser();
                if($type == 'golden'){
                    $user->setQuizPoints($user->getQuizPoints() - 50);
                } elseif($type == 'silver'){
                    $user->setQuizPoints($user->getQuizPoints() - 60);
                }
                $em = $this->getDoctrine()->getManager();
                $userPlayer = new UsersPlayers();
                $nbaPlayer = $player->findOneBy(['playerId' => $content->playerId]);
                $playerId = $nbaPlayer->getPlayerId();
                if(in_array($playerId, $playersIds)){
                    $user = $this->getUser();
                    $user->setQuizPoints($user->getQuizPoints() + 50);
                    continue;
                }
                $userPlayer->setPlayerId($nbaPlayer);
                $userPlayer->setUserId($this->getUser());
                $userPlayer->setPosition($nbaPlayer->getPosition());
                $userPlayer->setRating($nbaPlayer->getRating());
                if($nbaPlayer->getRating() > 85 && $nbaPlayer->getRating() <= 87){
                    $hint['rare'] = $nbaPlayer;
                } else if ($nbaPlayer->getRating() > 87 && $nbaPlayer->getRating() <= 92) {
                    $hint['very_rare'] = $nbaPlayer;
                } else if ($nbaPlayer->getRating() > 92 && $nbaPlayer->getRating() <= 94) {
                    $hint['ultra_rare'] = $nbaPlayer;
                } else if ($nbaPlayer->getRating() >= 95) {
                    $hint['epic'] = $nbaPlayer;
                }

                $em->persist($user);
                $em->persist($userPlayer);
                $em->flush();
            }

            return $this->render('starting5/pack/pack.html.twig', [
                'packContent' => $packContent,
                'hint' => $hint,
                'type' => $type
            ]);
        }

        return $this->render('starting5/pack/index.html.twig', [
            'silverForm' => $silverForm->createView(),
            'goldenForm' => $goldenForm->createView(),
            'count' => $countPlayers
        ]);
    }
}
