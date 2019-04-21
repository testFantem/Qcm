<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Response;



class QuestionReponseController extends AbstractController
{
    /**
     * Vérifie l'identifiant et le mot de passe fournit dans le formulaire
     * @Route("/createQuestion", name="createQuestion")
     */
    public function formCreateQuestion(Request $request)
    {
        $session = new Session();
        if($this->checkSession($request, $session)) {
            $createdMessage = '';
            $userInf['createdMessage'] = $createdMessage;
//          $userInf = array('username' => $session->get('username'), 'password' => $session->get('password'));
            return $this->render('/home/createQuestion.html.twig', $userInf);
        }
        else {
            $erreur['erreur'] = "Vous devez être connecté !";
            return $this->render('/home/home.html.twig', $erreur);
        }
    }

    /**
     * Vérifie l'identifiant et le mot de passe fournit dans le formulaire
     * @Route("/createQuestionToDB", name="createQuestionToDB")
     */
    public function createQuestionToDB(Request $request) {
        $session = new Session();

        // Si la session existe
        if($this->checkSession($request, $session))
        {
            /** @var User $user */

            $user = $this->getDoctrine()->getRepository(User::Class)->findOneByLogin($session->get('username'));
            $entityManager = $this->getDoctrine()->getManager();

            // Récupération des informations de la question
            $theme = $request->get('theme');
            $content = $request->get('content');

            // Création de la question
            $question = new Question();
            $question->setContent($content);
            $question->setAuthor($user);
            $question->setTheme($theme);

            $entityManager->persist($question);

            //On crée une réponse pour chaque input response present dans le formulaire
            $i = 1;
            $responseTmp = $request->get("response" . $i);
            while($responseTmp) {

                // Récupération des informations de la requête
                $responseRightAnswer = $request->get('response' . $i . 'TrueFalse');
                $responseContent = $request->get('response' . $i);

                // Création de la réponse
                $response = new Response();
                $response->setContent($responseContent);
                $response->setRightanswer(isset($responseRightAnswer));
                $question->addResponse($response);
                $entityManager->persist($response);
                $i++;

                //On affecte la nouvelle réponse à la variable responseTmp
                $responseTmp = $request->get("response" . $i);

            }
            $entityManager->flush();

            // actually executes the queries (i.e. the INSERT query)
            $userInf['createdMessage'] = 'Question créée avec succès';
            return $this->render('/home/createQuestion.html.twig', $userInf);
        }
        else
        {
            $erreur = array('erreur' => "");
            return $this->render('/home/home.html.twig', $erreur);
        }
    }
    private function checkSession(Request $request, Session $session)
    {
        return($request->getSession()->has('idUser'));
    }
}