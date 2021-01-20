<?php

namespace App\Controller;

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use	Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Event;
use App\Entity\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Symfony\Component\Filesystem\Filesystem;
use App\Controller\Response;


class CallendarController extends AbstractController
{
    /**
     * @Route("/callendar/{message}", name="callendar"))
     */
    public function index(string $message = null, FileUploader $fileuploader, Request $request)
    {
        $form = self::createEmptyForm(null, null, $request);
        $formSend = self::validateForm($form, $fileuploader);
        self::getEvents();
        if($message){
            $message = self::successMessage($message);
            return $this->render('callendar/index.html.twig',[
                'form' => $form->createView(),
                'message' => $message
            ]);
        }
        if(!$formSend){
            return $this->render('callendar/index.html.twig',[
                'form' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('callendar', ['message' => 'newEvent']);
        }

    }
    /**
     * @Route("/newEvent/{start}/{end}", name="newEvent")
     */
    public function newEvent(Request $request, string $start = null, string $end = null,  FileUploader $fileUploader){
    $form = self::createEmptyForm($start, $end, $request);
    $formSend = self::validateForm($form, $fileUploader);
    self::getEvents();

        if(!$formSend){
            return $this->render('callendar/index.html.twig',[
                'form' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('callendar', ['message' => 'newEvent']);
        }

    }
    /**
     * @Route("/updateEvent/{id}/{message}", name="updateEvent")
     */
    public function updateEvent(int $id, string $message=null, Request $request, Filesystem $fileSystem, FileUploader $fileUploader){

        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);

        $form = self::fillForm($event, $request);
        $formSend = self::validateFillForm($form, $id, $fileUploader, $fileSystem);

        self::getEvents();

        if(!$formSend){
            if($message){
                $message = self::successMessage($message);
                return $this->render('callendar/index.html.twig',[
                    'form' => $form->createView(),
                    'event' => $event,
                    'message' => $message
                ]);
            }
            return $this->render('callendar/index.html.twig',[
                'form' => $form->createView(),
                'event' => $event
            ]);
        }
        else{
            return $this->redirectToRoute('updateEvent', ['id'=>$id, 'message' => 'updateEvent']);
        }
    }

    /**
     * @Route("/deleteEvent/{id}", name="deleteEvent")
     */
    public function deleteEvent(int $id, Filesystem $filesystem){

        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        if($event->getFile()){
            $filesystem->remove('../public/files/'.$event->getFile()->getPath());
            $entityManager->remove($event->getFile());
        }

        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('callendar', ['message'=>'deleteEvent']);

    }

    /**
     * @Route("/deleteFile/{fileId}/{eventId}", name="deleteFile"))
     */
    public function deleteFile(int $fileId, int $eventId, Filesystem $filesystem)
    {

        $file = $this->getDoctrine()
            ->getRepository(File::class)
            ->find($fileId);

        $filesystem->remove('../public/files/'.$file->getPath());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($file);

        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($eventId);

        $event->setFile(null);
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('updateEvent', ['id'=>$eventId, 'message' => 'deleteFile']);
    }


    private function getEvents()
    {
        $events = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findAll();

        foreach ($events as $event) {
            if ($event->getStartDate()->format('Y-m-d') < date("Y-m-d") && $event->getDone() == false) {
                $backgroundColor = "red";
            } else {
                $backgroundColor = ($event->getDone() == false) ? "#0080008f" : "#686868";
            }

            $array = array(
                "id" => $event->getId(),
                "title" => $event->getTitle(),
                "name" => $event->getName(),
                "address" => $event->getAddress(),
                "phone" => $event->getPhone(),
                "start" => $event->getStartDate()->format('Y-m-d'),
                "end" => $event->getEndDate()->format('Y-m-d'),
                "description" => $event->getDescription(),
                "poolType" => $event->getPoolType(),
                "stairsPosition" => $event->getStairsPosition(),
                "done" => $event->getDone(),
                "backgroundColor" => $backgroundColor,
                "file_id" => $event->getFile()

            );
            array_push($events, $array);
        }
        $json = json_encode($events, JSON_PRETTY_PRINT);
        file_put_contents("./json/events.json", $json);
    }

    private function successMessage(string $message = null)
    {
        $succesMessage = "";
        switch ($message) {
            case null:
                break;
            case 'newEvent':
                $succesMessage = "Byla přidána nová objednávka!";
                break;
            case 'updateEvent':
                $succesMessage = "Objednávka byla upravena!";
                break;
            case 'deleteEvent':
                $succesMessage = "Objednávka byla smazána!";
                break;
            case 'deleteFile':
                $succesMessage = "Soubor byl smazán!";
                break;
            case 'cancelEvent':
                $succesMessage = "Vytvoření objednávky bylo zrušeno!";
                break;
            default:
                $succesMessage = "Někde je chyba!";
                break;
        }

        return $succesMessage;
    }

    private function createEmptyForm($start, $end, $request)
    {
        if (!($start && $end)) {
            $start = date('Y-m-d');
            $end = date("Y-m-d");
        }
        $form = $this->createFormBuilder()
            ->add('close', ButtonType::class, [
                'label' => "Zavřít",
                'attr' => ['class' => 'float-right btn-dark']
            ])
            ->add('title', TextType::class, [
                'label' => "Název",
            ])
            ->add('name', TextType::class, [
                'label' => "Jméno a příjmení",
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => "Adresa",
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => "Telefon",
                'attr' => ['pattern' => '[0-9]{9}', 'oninvalid' => 'this.setCustomValidity(\'Zadejte číslo ve formátu: 111222333!\')'],
                'required' => false
            ])
            ->add('startDate', DateType::class, [
                'label' => "Datum od",
                'data' => \DateTime::createFromFormat('Y-m-d', $start),
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => "Datum do",
                'data' => \DateTime::createFromFormat('Y-m-d', $end),
                'widget' => 'single_text',
            ])
            ->add('done', CheckboxType::class, [
                'label' => "Objednávka dokončena",
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => "Popis:",
                'required' => false
            ])
            ->add('file', FileType::class, [
                'label' => 'Vyberte soubor - pouze PDF',
                'required' => false
            ])
            ->add('poolType', ChoiceType::class, [
                'label' => "Vyberte tvar bazénu",
                'required' => false,
                'choices' => [
                    'Tvar bazénu' => [
                        'Vyber tvar bazénu' => null,
                        'Ovál' => '1',
                        'Obdélník' => '2',
                    ]
                ],
            ])
            ->add('stairsPosition', ChoiceType::class, [
                'label' => "Vyberte pozici schodů",
                'required' => false,
                'choices' => [
                    'Pozice schodů' => [
                        'Vyber pozici schodů' => null,
                        'Schody nalevo' => '1',
                        'Schody napravo' => '2',
                        'Schody přes celou šířku' => '3',
                    ],
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => "Nová objednávka",
                'attr' => ['class' => 'float-right btn-success']
            ])
            ->getForm();
        $form->handleRequest($request);
        return $form;

    }

    private function validateForm($form, $fileUploader)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $event = new Event();
            $file = new File();
            $fileData = $form->get('file')->getData();
            if ($fileData != null) {
                if ($fileData->guessExtension() == 'pdf') {
                    $fileName = $fileUploader->upload($fileData);
                    $file->setPath($fileName);
                    $entityManager->persist($file);
                    $entityManager->flush();

                    $event->setFile($file);
                } else {
                    $file = null;
                }
            }

            $event
                ->setTitle($form->get('title')->getData())
                ->setName($form->get('name')->getData())
                ->setAddress($form->get('address')->getData())
                ->setPhone($form->get('phone')->getData())
                ->setStartDate($form->get('startDate')->getData())
                ->setEndDate($form->get('endDate')->getData())
                ->setDescription($form->get('description')->getData())
                ->setDone($form->get('done')->getData())
                ->setPoolType($form->get('poolType')->getData())
                ->setStairsPosition($form->get('stairsPosition')->getData());

            $entityManager->persist($event);
            $entityManager->flush();

            return true;
        }
        return false;
    }

    private function fillForm($event, $request)
    {
        $form = $this->createFormBuilder()
            ->add('close', ButtonType::class, [
                'label' => "Zavřít",
                'attr' => ['class' => 'float-right btn-dark']
            ])
            ->add('title', TextType::class, [
                'label' => "Název",
                'data' => $event->getTitle(),
            ])
            ->add('name', TextType::class, [
                'label' => "Jméno a příjmení",
                'required' => false,
                'data' => $event->getName(),
            ])
            ->add('address', TextType::class, [
                'label' => "Adresa",
                'data' => $event->getAddress(),
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => "Telefon",
                'attr' => ['pattern' => '[0-9]{9}', 'oninvalid' => 'this.setCustomValidity(\'Zadejte číslo ve formátu: 111222333!\')'],
                'data' => $event->getPhone(),
                'required' => false
            ])
            ->add('startDate', DateType::class, [
                'label' => "Datum od:",
                'data' => $event->getStartDate(),
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => "Datum do:",
                'data' => $event->getEndDate(),
                'widget' => 'single_text',
            ])
            ->add('done', CheckboxType::class, [
                'label' => "Objednávka dokončena",
                'data' => $event->getDone(),
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => "Popis:",
                'data' => $event->getDescription(),
                'required' => false
            ])
            ->add('file', FileType::class, [
                'required' => false
            ])
            ->add('poolType', ChoiceType::class, [
                'label' => "Vyberte tvar bazénu",
                'data' => $event->getPoolType(),
                'required' => false,
                'choices' => [
                    'Tvar bazénu' => [
                        'Vyber tvar bazénu' => null,
                        'Ovál' => '1',
                        'Obdélník' => '2',
                    ]
                ],
            ])
            ->add('stairsPosition', ChoiceType::class, [
                'label' => "Vyberte pozici schodů",
                'required' => false,
                'data' => $event->getStairsPosition(),
                'choices' => [
                    'Pozice schodů' => [
                        'Vyber pozici schodů' => null,
                        'Schody nalevo' => '1',
                        'Schody napravo' => '2',
                        'Schody přes celou šířku' => '3',
                    ],
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => "Uložit objednávku",
                'attr' => ['class' => 'float-right btn-success']
            ])
            ->add('delete', ButtonType::class, [
                'attr' => ['data_id' => $event->getId(), 'class' => 'float-right btn-danger'],
                'label' => "Smazat objednávku"
            ])
            ->getForm();

        $form->handleRequest($request);
        return $form;
    }

    private function validateFillForm($form, $id, $fileUploader, $fileSystem)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $file = new File();

            $event = $this->getDoctrine()
                ->getRepository(Event::class)
                ->find($id);

            $currentFile = $event->getFile();

            $fileData = $form->get('file')->getData();
            if ($fileData != null) {
                if ($fileData->guessExtension() == 'pdf') {
                    if ($currentFile != null) {
                        $fileSystem->remove('../public/files/' . $currentFile->getPath());
                        $entityManager->remove($currentFile);
                        $entityManager->flush();
                    }

                    $fileName = $fileUploader->upload($fileData);
                    $file->setPath($fileName);

                    $event->setFile($file);
                }
            }

            $event->setTitle($form->get('title')->getData());
            $event->setName($form->get('name')->getData());
            $event->setAddress($form->get('address')->getData());
            $event->setPhone($form->get('phone')->getData());
            $event->setStartDate($form->get('startDate')->getData());
            $event->setEndDate($form->get('endDate')->getData());
            $event->setDescription($form->get('description')->getData());
            $event->setDone($form->get('done')->getData());
            $event->setPoolType($form->get('poolType')->getData());
            $event->setStairsPosition($form->get('stairsPosition')->getData());
            $entityManager->persist($event);
            $entityManager->flush();

            return true;
        }
        return false;
    }
}
