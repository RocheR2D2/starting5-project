<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $playerRepository = $this->getDoctrine()->getRepository(Player::class);
        $players = $playerRepository->findAll();
        $player = new Player();

        $form = $this->createFormBuilder($player)
            ->add('firstname', TextType::class, array('label' => 'Firstname of Player'))
            ->add('lastname', TextType::class, array('label' => 'Lastname of Player'))
            ->add('slugPlayer', TextType::class, array('label' => 'Slug of Player'))
            ->add('shirtNumber', NumberType::class, array('label' => '# of Player'))
            ->add('nbaDebut', NumberType::class, array('label' => 'NBA Debut'))
            ->add('born', BirthdayType::class, array(
                'label' => 'Date of Birth',
                'years' => range(date('Y') - 40, date('Y'))
            ))
            ->add('fGPercentage', NumberType::class, array('label' => 'FG%'))
            ->add('threePointsPercentage', NumberType::class, array('label' => '3P%'))
            ->add('fTPercentage', NumberType::class, array('label' => 'FT%'))
            ->add('PPG', NumberType::class, array('label' => 'PPG'))
            ->add('RPG', NumberType::class, array('label' => 'RPG'))
            ->add('APG', NumberType::class, array('label' => 'APG'))
            ->add('BPG', NumberType::class, array('label' => 'BPG'))
            ->add('height', NumberType::class, array('label' => 'Height'))
            ->add('weight', NumberType::class, array('label' => 'Weight'))
            ->add('team', EntityType::class, array(
                'label' => 'Select Team',
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
            ))
            ->add('position', EntityType::class, array(
                'label' => 'Select Position',
                'class' => 'AppBundle:Position',
                'choice_label' => 'name',
            ))
            ->add('state', EntityType::class, array(
                'label' => 'Select Native State of Player',
                'class' => 'AppBundle:State',
                'choice_label' => 'stateName',
            ))
            ->add('save', SubmitType::class, array('label' => 'Save Player'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $player = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            return $this->redirectToRoute('player.new');
        }

        return $this->render('starting5/admin/player/new.html.twig', array(
            'form' => $form->createView(),
            'players' => $players
        ));
    }

    public function editAction(Request $request, $id)
    {
        $playerRepository = $this->getDoctrine()->getRepository(Player::class);
        $player = $playerRepository->find($id);
        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $player = $form->getData();
            $em->persist($player);
            $em->flush();

            return $this->redirectToRoute('player.edit', ['id' => $id]);
        }

        return $this->render('starting5/admin/player/edit.html.twig', array(
            'form' => $form->createView(),
            'player' => $player,
            'id' => $id
        ));
    }
}