<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NBAPlayers;
use AppBundle\Entity\UsersPlayers;
use AppBundle\Entity\UserTeam;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $form = $this->createFormBuilder()
            ->add('save', SubmitType::class, array('label' => 'OPEN A PACK'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $userPlayers = $userPlayerRepository->findBy(['userId' => $this->getUser()->getId()]);
            $playersIds = [];
            foreach ($userPlayers as $userPlayer) {
                $playersIds[] = $userPlayer->getPlayerId()->getPlayerId();
            }
            $packContent = $player->packOpener($this->getUser());

            foreach ($packContent as $content) {
                $user = $this->getUser();
                $user->setQuizPoints($user->getQuizPoints() - 50);
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

                $em->persist($user);
                $em->persist($userPlayer);
                $em->flush();
            }
            return $this->render('starting5/pack/pack.html.twig', [
                'packContent' => $packContent,
            ]);
        }

        return $this->render('starting5/pack/index.html.twig', [
            'form' => $form->createView(),
            'count' => $countPlayers
        ]);
    }
}
