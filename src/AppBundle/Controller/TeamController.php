<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $teamRepository = $this->getDoctrine()->getRepository(Team::class);
        $teams = $teamRepository->findAll();
        $team = new Team();

        $form = $this->createFormBuilder($team)
            ->add('name', TextType::class, array('label' => 'Name of Team'))
            ->add('slugTeam', TextType::class, array('label' => 'Slug of Team'))
            ->add('conference', EntityType::class, array(
            'label' => 'Select Conference',
            'class' => 'AppBundle:Conference',
            'choice_label' => 'name',
            ))
            ->add('division', EntityType::class, array(
                'label' => 'Select Division',
                'class' => 'AppBundle:Division',
                'choice_label' => 'name',
            ))
            ->add('state', EntityType::class, array(
                'label' => 'Select State',
                'class' => 'AppBundle:State',
                'choice_label' => 'stateName',
            ))
            ->add('town', EntityType::class, array(
                'label' => 'Select Town',
                'class' => 'AppBundle:Town',
                'choice_label' => 'name',
            ))
            ->add('stadium', EntityType::class, array(
                'label' => 'Select Stadium',
                'class' => 'AppBundle:Stadium',
                'choice_label' => 'name',
            ))
            ->add('save', SubmitType::class, array('label' => 'Create Team'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $team = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('team.new');
        }

        return $this->render('starting5/admin/team/new.html.twig', array(
            'form' => $form->createView(),
            'teams' => $teams
        ));
    }
}