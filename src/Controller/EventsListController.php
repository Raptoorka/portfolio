<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // ... adding the name field if needed
        })
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
            $events = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findByDate($form->get('startDate')->getData(), $form->get('endDate')->getData());
            return $this->render('eventsList/index.html.twig',[
              'form' => $form->createView(),
              'events' => $events,
            ]);
          }



        return $this->render('eventsList/index.html.twig',[
          'form' => $form->createView(),
        ]);
    }
}
