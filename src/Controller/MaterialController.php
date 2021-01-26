<?php

namespace App\Controller;

use App\Entity\Accessory;
use App\Entity\Material;
use App\Form\AccessoryType;
use App\Form\MaterialType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaterialController extends AbstractController
{
    /**
     * @Route("/material", name="material")
     */
    public function index(Request $request): Response
    {
        $material = new Material();
        $accessory = new Accessory();

        $formMaterial = $this->createForm(MaterialType::class, $material);
        $formMaterial->handleRequest($request);
        if ($formMaterial->isSubmitted() && $formMaterial->isValid()) {
            $materialData = $formMaterial->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materialData );
            $entityManager->flush();
            return $this->redirectToRoute("material");
        }

        $formAccessory = $this->createForm(AccessoryType::class, $accessory);
        $formAccessory->handleRequest($request);

        if ($formAccessory->isSubmitted() && $formAccessory->isValid()) {
            $accessoryData = $formAccessory->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($accessoryData );
            $entityManager->flush();
            return $this->redirectToRoute("material");
        }

        $materials = $this->getDoctrine()->getRepository(Material::class)->findAll();
        $accessories = $this->getDoctrine()->getRepository(Accessory::class)->findAll();
        return $this->render('material/index.html.twig', [
            'accessories' => $accessories,
            'materials' => $materials,
            'formMaterial' => $formMaterial->createView(),
            'formAccessory' => $formAccessory->createView()
        ]);
    }

    /**
     * @Route("/materialUpdate/{id}", name="materialUpdate")
     */
    public function materialUpdate(int $id, Request $request): Response
    {
        $entityRepository = $this->getDoctrine()->getRepository(Material::class);

        $material = $entityRepository->find($id);

        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $event = $form->getData();
            if ($form->get('delete')->isClicked()) {
                $entityManager->remove($event);
            } else {
                $entityManager->persist($event);
            }
            $entityManager->flush();
            return $this->redirectToRoute("material");
        }

        return $this->render('material/updateMaterial.html.twig', [
            'formMaterial' => $form->createView(),
            'materialId' => $id
        ]);
    }

    /**
     * @Route("/accessoryUpdate/{id}", name="accesoryUpdate")
     */
    public function accessoryUpdate(int $id, Request $request): Response
    {
        $entityRepository = $this->getDoctrine()->getRepository(Accessory::class);


        $accessory = $entityRepository->find($id);

        $form = $this->createForm(AccessoryType::class, $accessory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $event = $form->getData();
            if ($form->get('delete')->isClicked()) {
                $entityManager->remove($event);
            } else {
                $entityManager->persist($event);
            }
            $entityManager->flush();
            return $this->redirectToRoute("material");
        }

        return $this->render('material/updateAccessory.html.twig', [
            'formAccessory' => $form->createView(),
            'accessoryId' => $id
        ]);
    }
}
