<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
      /*$entityManager = $this->getDoctrine()->getManager();

      $user = new User();
      $user->setName("First user name");
      $user->setSurname("First user surname");
      $user->setAddress("First user address");

      $entityManager->persist($user);

      $entityManager->flush();*/


        return $this->render('test/index.html.twig', [
            'user' => "test",
        ]);
    }

}
