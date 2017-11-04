<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stadium;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class StadiumController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $stadiumRepository = $this->getDoctrine()->getRepository(Stadium::class);
        $stadiums = $stadiumRepository->findAll();
        $stadium = new Stadium();

        $form = $this->createFormBuilder($stadium)
            ->add('name', TextType::class, array('label' => 'Name of Stadium'))
            ->add('slugStadium', TextType::class, array('label' => 'Slug of Stadium'))
            ->add('save', SubmitType::class, array('label' => 'Create Stadium'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $stadium = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em = $this->getDoctrine()->getManager();
            $em->persist($stadium);
            $em->flush();

            return $this->redirectToRoute('stadium.new');
        }

        return $this->render('starting5/admin/stadium/new.html.twig', array(
            'form' => $form->createView(),
            'stadiums' => $stadiums
        ));
    }
}