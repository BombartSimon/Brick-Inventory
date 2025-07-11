<?php

namespace App\Controller;

use App\Entity\SetPart;
use App\Form\SetPartType;
use App\Repository\SetPartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/set/part')]
final class SetPartController extends AbstractController
{
    #[Route(name: 'app_set_part_index', methods: ['GET'])]
    public function index(SetPartRepository $SetPartRepository): Response
    {
        return $this->render('set_part/index.html.twig', [
            'set_parts' => $SetPartRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_set_part_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $SetPart = new SetPart();
        $form = $this->createForm(SetPartType::class, $SetPart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($SetPart);
            $entityManager->flush();

            return $this->redirectToRoute('app_set_part_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('set_part/new.html.twig', [
            'set_part' => $SetPart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_set_part_show', methods: ['GET'])]
    public function show(SetPart $SetPart): Response
    {
        return $this->render('set_part/show.html.twig', [
            'set_part' => $SetPart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_set_part_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SetPart $SetPart, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SetPartType::class, $SetPart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_set_part_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('set_part/edit.html.twig', [
            'set_part' => $SetPart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_set_part_delete', methods: ['POST'])]
    public function delete(Request $request, SetPart $SetPart, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $SetPart->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($SetPart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_set_part_index', [], Response::HTTP_SEE_OTHER);
    }
}
