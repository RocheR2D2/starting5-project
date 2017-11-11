<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Division;
use AppBundle\Entity\Player;
use AppBundle\Entity\UserDivision;
use AppBundle\Entity\UserTeam;
use AppBundle\Entity\UserTopTeam;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $userTeamDoctrine = $this->getDoctrine()->getRepository(UserTeam::class);
        $userTeams = $userTeamDoctrine->findBy(['user' => $user]);
        return $this->render('starting5/dashboard/index.html.twig', [
            'name' => "Starting 5",
            'userTeams' => $userTeams
        ]);
    }

    public function newAction(Request $request){
        $user = $this->getUser();

        $playerDoctrine = $this->getDoctrine()->getRepository(Player::class);

        $userTeamDoctrine = $this->getDoctrine()->getRepository(UserTeam::class);
        $userTeams = $userTeamDoctrine->findAll();
        $userTeam = new UserTeam();

        $userDivisionRepository = $this->getDoctrine()->getRepository(UserDivision::class);

        $userIsTopRepository = $this->getDoctrine()->getRepository(UserTopTeam::class);
        $userTopTeam = $userIsTopRepository->findBy(['user' => $user->getId()]);
        $topTeams = $this->getUserTopTeams($userTopTeam);

        $userDivisions = $userDivisionRepository->findBy(['user' => $user->getId()]);

        $form = $this->createFormBuilder($userTeam)
            ->add('name', TextType::class, array('label' => 'Name of team'))
            ->add('stadiumId', EntityType::class, array(
                'label' => 'Select Stadium',
                'class' => 'AppBundle:Stadium',
                'choice_label' => 'name',
            ))
            ->add('trainerId', EntityType::class, array(
                'label' => 'Select Trainer',
                'class' => 'AppBundle:Trainer',
                'choice_label' => 'fullName',
            ))
            ->add('save', SubmitType::class, array('label' => 'Create My Team'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $data = $request->request->get('form');
            $userTeam = $form->getData();
            $userTeam->setPointGuard($playerDoctrine->find($data['pointGuard']));
            $userTeam->setShootingGuard($playerDoctrine->find($data['shootingGuard']));
            $userTeam->setSmallForward($playerDoctrine->find($data['smallForward']));
            $userTeam->setPowerForward($playerDoctrine->find($data['powerForward']));
            $userTeam->setCenter($playerDoctrine->find($data['center']));
            $userTeam->setUser($user);
            $userTeam->setLike(0);
            $userTeam->setDislike(0);

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em = $this->getDoctrine()->getManager();
            $em->persist($userTeam);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('starting5/dashboard/new.html.twig', array(
            'form' => $form->createView(),
            'userTeams' => $userTeams,
            'userDivisions' => $userDivisions,
            'userTopTeams' => $topTeams,
        ));
    }

    public function getUserTopTeams($userTopTeam){
        /** @var array $topTeams */
        $topTeams = [];
        foreach ($userTopTeam as $topTeam) {
            $topTeams[] = $topTeam->getTeam()->getId();
        }

        return $topTeams;
    }
}
