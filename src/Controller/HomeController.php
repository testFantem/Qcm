<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;


class HomeController extends AbstractController
{

    /**
     * @Route("/", name="HomeController")
     */
    public function home(Request $request)
    {
        $session = new Session();
        // Si la session existe
        if($this->checkSession($request, $session))
        {

            /** @var User $user */
            $user = $this->getDoctrine()->getRepository(User::Class)->findOneByLogin($session->get('username'));
            $userInf =
                array('username' => $user->getLogin(),
                    'password' => $user->getPassword(),
                    'role' => $user->getRole(),
                    'email' => $user->getEmail());

            return $this->render('/home/test.html.twig', $userInf);
        }
        else
        {
            $erreur = array('erreur' => "");
            return $this->render('/home/home.html.twig', $erreur);
        }
    }

    /**
     * Vérifie l'identifiant et le mot de passe fournit dans le formulaire
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {
        $erreur = array('erreur' => "");
        $session = new Session();
        if($this->checkSession($request, $session))
        {
            $user = $this->getDoctrine()->getRepository(User::Class)->findOneByLogin($session->get('username'));
            $userInf =
                array('username' => $user->getLogin(),
                    'password' => $user->getPassword(),
                    'role' => $user->getRole(),
                    'email' => $user->getEmail());
            return $this->render('/home/test.html.twig', $userInf);
        }
        else
        {
            // Récupération des éléments du formulaire de l'utilisateur
            $username = $request->get('username');
            $password = $request->get('password');

            // Création d'un tableau associatif pour stocker les informations de l'utilisateur
            $userInf = array('username' => $username, 'password' => $password);

            // Vérification de l'existence des données de l'utilisateur
            if(isset($username) && isset($password) && !empty($username) && !empty($password))
            {
                $user = $this->getDoctrine()->getRepository(User::Class)->findOneByLogin($username);
                // Vérification dans la base de données les identifiants
                if($user !== null) {
                    if($user->getPassword() === $password)
                    {
                        $session->set('idUser', $user->getId());
                        $session->set('username', $username);
                        $session->set('password', $password);
                        $session->set('etat', "Connexion");

                        $userInf['etat'] = "Connexion ok";

                        // Affichage d'un twig en fonction du rôle de l'utilisateur
                        $user = $this->getDoctrine()->getRepository(User::Class)->findOneByLogin($session->get('username'));
                        $userInf =
                            array('username' => $user->getLogin(),
                                'password' => $user->getPassword(),
                                'role' => $user->getRole(),
                                'email' => $user->getEmail());
                        return $this->render('/home/test.html.twig', $userInf);
                    }
                    else
                    {
                        $erreur['erreur'] = "Le mot de passe est incorrect";
                        return $this->render('/home/home.html.twig', $erreur);
                    }
                } else {
                    return $this->render('/home/home.html.twig', $erreur);
                }
            }
            else
            {
                $erreur['erreur'] = "Il manque des champs";
                return $this->render('/home/home.html.twig', $erreur);
            }
        }
    }

    /**
     * Vérifie si la session existe et redirige sur la bonne page
     * @param Request $request
     * @param Session $session
     * @return bool
     */
    private function checkSession(Request $request, Session $session)
    {
        return($request->getSession()->has('idUser'));
    }


}