<?php

namespace AppBundle\Controller;

use SensioLabs\Security\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Quizz;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class QuizzController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render("starting5/quizz/index.html.twig");
    }

    public function getRandomQuizzAction()
    {
        $em = $this->getDoctrine()->getManager();
        $quizzs = $em->getRepository("AppBundle:Quizz")->findAll();
        shuffle($quizzs);
        $quizz = new Quizz();
        $quizz = $quizzs[0];
        if($quizz->getType() == 'Question')
        {
            $quizz->setAnswer1(null);
        }
        $serializer = $this->get('serializer');
        $response = $serializer->serialize($quizz,'json');
        return new Response($response);
    }

    public function validateQuizzAction(Request $request)
    {
        $Req = json_decode($request->getContent());
        $id = $Req->id;
        if (null === $id) {
            throw new NotFoundHttpException("L'id n'existe pas.");
        }
        $answer = $Req->answer;
        if (null === $answer) {
            throw new NotFoundHttpException("La réponse n'existe pas.");
        }

        $em = $this->getDoctrine()->getManager();
        $quizz = $em->getRepository("AppBundle:Quizz")->find($id);

        if($quizz->getType() == 'QCM' && $quizz->getQCMAnswer() == $answer){
            return new Response("true");
        }else if($quizz->getType() == 'Question' && $quizz->getAnswer1() == $answer){
            return new Response("true");
        }else{
            return new Response("false");
        }
        return new Response("Erreur");
    }
}

?>