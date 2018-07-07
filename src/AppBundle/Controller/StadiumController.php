<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stadium;
use AppBundle\Entity\UserStadium;
use AppBundle\Entity\UserTrainer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StadiumController extends Controller
{
    private $em;
    private $userStadium;
    private $userTrainer;

    public function __construct(ObjectManager $objectManager)
    {
        $this->em = $objectManager;
        $this->userStadium = $this->em->getRepository(UserStadium::class);
        $this->userTrainer = $this->em->getRepository(UserTrainer::class);
    }

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

    public function myStadiumsAction()
    {
        $user = $this->getUser();
        $myStadiums = $this->userStadium->findBy(['userId' => $user]);
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($myStadiums, 'json');
        $response = new Response($result);

        return $response;
    }

    public function myTrainersAction()
    {
        $user = $this->getUser();
        $myTrainers = $this->userTrainer->findBy(['userId' => $user]);
        $serializer = $this->container->get('serializer');
        $result = $serializer->serialize($myTrainers, 'json');
        $response = new Response($result);

        return $response;
    }
}