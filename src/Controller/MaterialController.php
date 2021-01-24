<?php

namespace App\Controller;

use App\Entity\Material;
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
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $materialData = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materialData );
            $entityManager->flush();
            return $this->redirectToRoute("material");
        }
        $materials = $this->getDoctrine()->getRepository(Material::class)->findAll();
        return $this->render('material/index.html.twig', [
            'materials' => $materials,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/material/{id}", name="materialUpdate")
     */
    public function materialUpdate(int $id, Request $request): Response
    {
        $entityRepository = $this->getDoctrine()->getRepository(Material::class);
        $materials = $entityRepository->findAll();

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

        return $this->render('material/index.html.twig', [
            'materials' => $materials,
            'form' => $form->createView(),
            'id' => $id
        ]);
    }
}
