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

        if(sizeof($quizzs) < 3){
            throw new NotFoundHttpException("Not enough quizz");
        }

        shuffle($quizzs);
        $quizz = array($quizzs[0],$quizzs[1],$quizzs[2]);

        foreach($quizz as $quiz){
            if($quiz->getType() == 'Question')
            {
                $quiz->setAnswer1(null);
            }
            else if($quiz->getType() == 'QCM')
            {
                $quiz->setQCMAnswer(null);
            }
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