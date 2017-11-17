<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Form\TeamType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $teamsJson = file_get_contents('http://data.nba.net/prod/v1/2017/teams.json');
        $teamsDecode = json_decode($teamsJson);
        $teams = $teamsDecode->league->standard;
        return $this->render('starting5/team/index.html.twig', [
            'teams' => $teams
        ]);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $teamRepository = $this->getDoctrine()->getRepository(Team::class);
        $teams = $teamRepository->findAll();
        $team = new Team();

        $form = $this->createFormBuilder($team)
            ->add('name', TextType::class, array('label' => 'Name of Team'))
            ->add('slugTeam', TextType::class, array('label' => 'Slug of Team'))
            ->add('isTop', CheckboxType::class, array('label' => 'Is top of the division ?', 'required' => false))
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
            $team = $form->getData();

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

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $teamRepository = $this->getDoctrine()->getRepository(Team::class);
        $team = $teamRepository->find($id);
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $team = $form->getData();
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('team.edit', ['id' => $id]);
        }

        return $this->render('starting5/admin/team/edit.html.twig', array(
            'form' => $form->createView(),
            'team' => $team,
            'id' => $id
        ));
    }
}