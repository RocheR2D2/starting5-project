<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Quizz;
use AppBundle\Form\QuizzType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('starting5/admin/index.html.twig', [
            'name' => "Admin",
        ]);
    }

    public function quizzAction()
    {
        return $this->render('starting5/admin/quizz/index.html.twig');
    }

    public function quizzFormAction(Request $request)
    {
        $quizz = new Quizz();
        $form = $this->createForm(QuizzType::class,$quizz, array(
            'action' => $this->generateUrl('admin.quizzForm'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($quizz);
                $em->flush();
                return $this->render('starting5/admin/quizz/index.html.twig');
            }
        }
        return $this->render('starting5/admin/quizz/quizzAddForm.html.twig', array('form' => $form->createView()));
    }

    public function getAllQuizzAction()
    {
        $em = $this->getDoctrine()->getManager();
        $allQuizz =  $em->getRepository("AppBundle:Quizz")->findAll();
        return $this->render('starting5/admin/quizz/allQuizz.html.twig', array('allQuizz' => $allQuizz));
    }
}
