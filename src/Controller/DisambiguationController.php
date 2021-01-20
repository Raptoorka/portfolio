<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DisambiguationController extends AbstractController
{
    /**
     * @Route("/disambiguation", name="disambiguation")
     */
    public function index()
    {
        return $this->render('disambiguation/index.html.twig');
    }
}
