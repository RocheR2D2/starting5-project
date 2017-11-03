<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Division;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DivisionController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $divisionRepository = $this->getDoctrine()->getRepository(Division::class);
        $divisions = $divisionRepository->findAll();
        $division = new Division();

        $form = $this->createFormBuilder($division)
            ->add('name', TextType::class, array('label' => 'Name of Division'))
            ->add('slugDivision', TextType::class, array('label' => 'Slug of Division'))
            ->add('conference', EntityType::class, array(
                'label' => 'Select Conference',
                'class' => 'AppBundle:Conference',
                'choice_label' => 'name',
            ))
            ->add('save', SubmitType::class, array('label' => 'Create Division'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $division = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em = $this->getDoctrine()->getManager();
            $em->persist($division);
            $em->flush();

            return $this->redirectToRoute('division.new');
        }

        return $this->render('starting5/admin/division/new.html.twig', array(
            'form' => $form->createView(),
            'divisions' => $divisions
        ));
    }
}
