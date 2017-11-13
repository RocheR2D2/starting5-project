<?php

namespace AppBundle\Controller;

use AppBundle\Entity\State;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class StateController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $stateRepository = $this->getDoctrine()->getRepository(State::class);
        $states = $stateRepository->findAll();
        $state = new State();

        $form = $this->createFormBuilder($state)
            ->add('stateName', TextType::class, array('label' => 'Name of State'))
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
            ->add('save', SubmitType::class, array('label' => 'Create State'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $state = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
             $em = $this->getDoctrine()->getManager();
             $em->persist($state);
             $em->flush();

            return $this->redirectToRoute('state.new');
        }

        return $this->render('starting5/admin/state/new.html.twig', array(
            'form' => $form->createView(),
            'states' => $states
        ));
    }
}
