<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\QuestionList;
use App\Entity\Qcm;
use App\Entity\Response;



class CreateQcmController extends AbstractController
{
    /**
     * Insert dans la base de données le nouveau qcm et la liste de questions qu'il contient
     * @Route("/createQcm", name="CreateQcm")
     */
    public function createQcm(Request $request)
    {
        $session = new Session();

        if($this->checkSession($request, $session))
        {
            $repository = $this->getDoctrine()->getRepository(Question::class);
            $questions = $repository->findAll();
            if($request->get('idQuestion') === null) {
                return $this->render('/home/createQcm.html.twig', [
                    "questions" => $questions
                ]);
            } else {
                $questionsRequest = $request->get('idQuestion');
                $user = $this->getDoctrine()->getRepository(User::Class)->findOneByLogin($session->get('username'));

                $entityManager = $this->getDoctrine()->getManager();

                $qcm = new Qcm();
                $qcm->setAuthorId($user);
                $qcm->setDeadline(new \DateTime());
                $qcm->setVisible(0);
                $entityManager->persist($qcm);

                foreach($questionsRequest as $question) {
                    $questionList = new QuestionList();
                    $questionTmp = $this->getDoctrine()->getRepository(Question::Class)->find($question);
                    $questionList->setQuestionId($questionTmp);
                    $questionList->setQcmId($qcm);
                    $entityManager->persist($questionList);
                }
                $entityManager->flush();

                return $this->render('/home/createQcm.html.twig', [
                    "questions" => $questions
                ]);
            }
        } else {
            $erreur = array('erreur' => "Veullez vous identifier");
            return $this->render('/home/home.html.twig', $erreur);
        }
    }

    private function checkSession(Request $request, Session $session)
    {
        return($request->getSession()->has('idUser'));
    }

}