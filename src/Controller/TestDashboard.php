<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Response;



class TestDashboard extends AbstractController
{

    /**
     * @Route("/test", name="testDashboard")
     */
    public function testDashboard(Request $request)
    {
        return $this->render("/home/dashboard.html.twig");
    }
    
}