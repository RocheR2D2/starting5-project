<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class QuizzController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render("starting5/quizz/index.html.twig");
    }

    public function getRandomQuizzAction()
    {
        $em = $this->getDoctrine()->getManager();
        $quizz = $em->getRepository("AppBundle:Quizz")->findAll();
        shuffle($quizz);

        $serializer = $this->get('serializer');
        $response = $serializer->serialize($quizz[0],'json');
        return new Response($response);
    }
}

?>