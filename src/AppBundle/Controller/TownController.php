<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Town;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class TownController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $townRepository = $this->getDoctrine()->getRepository(Town::class);
        $towns = $townRepository->findAll();
        $town = new Town();

        $form = $this->createFormBuilder($town)
            ->add('name', TextType::class, array('label' => 'Name of Town'))
            ->add('slugTown', TextType::class, array('label' => 'Slug of Town'))
            ->add('save', SubmitType::class, array('label' => 'Create Town'))
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

            return $this->redirectToRoute('town.new');
        }

        return $this->render('starting5/admin/town/new.html.twig', array(
            'form' => $form->createView(),
            'towns' => $towns
        ));
    }
}