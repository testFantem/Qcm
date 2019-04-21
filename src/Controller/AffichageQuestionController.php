<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Response;



class AffichageQuestionController extends AbstractController
{

    /**
     * Vérifie l'identifiant et le mot de passe fournit dans le formulaire
     * @Route("/showQuestions", name="afficherQuestions")
     */
    public function afficherQuestions(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findAll();
        return $this->render('/home/showQuestions.html.twig', [
            "questions" => $questions
        ]);
    }
    
}