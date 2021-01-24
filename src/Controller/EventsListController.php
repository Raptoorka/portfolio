<?php

namespace App\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Event;

class EventsListController extends AbstractController
{
    /**
     * @Route("/eventsList", name="eventsList")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder()
        ->add('startDate', DateType::class,[
          'label' => "Datum od",
          'widget' => 'single_text',
        ])
        ->add('endDate', DateType::class,[
          'label' => "Datum do",
          'widget' => 'single_text',
        ])
        ->add('save', SubmitType::class,[
          'label' => "Ukázat",
          'attr' => ['class'=>'btn-success'],
          'disabled' => true
        ])
        ->getForm();

        $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
              return $this->redirectToRoute("getEventsList", ['startDate'=>$form->get('startDate')->getData()->format("Y-m-d"), 'endDate'=>$form->get('endDate')->getData()->format("Y-m-d"), 'page'=>1]);

          }
        return $this->render('eventsList/index.html.twig',[
          'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/getEventsList/{startDate}/{endDate}/{page}", name="getEventsList")
     */
    public function getEventsList(string $startDate, string $endDate, int $page)
    {
        $pageLimit = 4;
        $firstResult = $page*$pageLimit-$pageLimit;
        $query=$this->getDoctrine()->getRepository(Event::class);
        $query = $query->createQueryBuilder("e")
            ->andWhere('e.startDate >= :startDate')
            ->setParameter('startDate', $startDate)
            ->andWhere('e.endDate <= :endDate')
            ->andWhere('e.done=false')
            ->setParameter('endDate', $endDate)
            ->setFirstResult($firstResult)
            ->setMaxResults($pageLimit)
            ->getQuery();


        $paginator = new Paginator($query);
        $prev = ($page>1 && $page<=count($paginator))? $page-1 : " ";
        $next = ($page*$pageLimit<count($paginator))? $page+1 : " ";


        return $this->render('eventsList/index.html.twig',[
            'paginator' => $paginator,
            'prev' => $prev,
            'next' => $next,
            'page' => $page
        ]);
    }
}
