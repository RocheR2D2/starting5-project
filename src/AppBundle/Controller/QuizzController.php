<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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

        $answer = $Req->answer;
        if (null === $answer) {
            throw new NotFoundHttpException("La réponse n'existe pas.");
        }

        $em = $this->getDoctrine()->getManager();

        $validQuizz = array();

        foreach($answer as $quizzAnswer){


            $quizz = $em->getRepository("AppBundle:Quizz")->find($quizzAnswer->id);
            if($quizz->getType() == 'QCM' && $quizz->getQCMAnswer() == trim(strtolower($quizzAnswer->QCMAnswer))){
                array_push($validQuizz, array("value" => true));
            }else if($quizz->getType() == 'Question' && trim(strtolower($quizz->getAnswer1())) == trim(strtolower($quizzAnswer->QCMAnswer))){
                array_push($validQuizz, array("value" => true));
            }else{
                array_push($validQuizz,array("value" => false));
            }
        }

        $user = $this->getUser();
        $total = 0;
        $winPoint = 500;

        foreach ($validQuizz as $valid){
            if($valid["value"] == true){
                $total += $winPoint;
            }
        }

        $user->setQuizPoints($user->getQuizPoints() + $total);

        $em->persist($user);
        $em->flush();

        $results = array();

        array_push($results, $validQuizz);
        array_push($results, $total);

        $serializer = $this->get('serializer');
        $response = $serializer->serialize($results,'json');
        return new Response($response);

    }
}

?>