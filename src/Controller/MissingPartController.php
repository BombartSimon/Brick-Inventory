<?php

namespace App\Controller;

use App\Entity\MissingPart;
use App\Entity\Set;
use App\Form\MissingPartType;
use App\Repository\MissingPartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/missing/part')]
final class MissingPartController extends AbstractController
{
    #[Route(name: 'app_missing_part_index', methods: ['GET'])]
    public function index(MissingPartRepository $missingPartRepository): Response
    {
        return $this->render('missing_part/index.html.twig', [
            'missing_parts' => $missingPartRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_missing_part_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $missingPart = new MissingPart();
        $form = $this->createForm(MissingPartType::class, $missingPart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($missingPart);
            $entityManager->flush();

            return $this->redirectToRoute('app_missing_part_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('missing_part/new.html.twig', [
            'missing_part' => $missingPart,
            'form' => $form,
        ]);
    }

    #[Route('/new/for-set/{id}', name: 'app_missing_part_new_for_set', methods: ['GET', 'POST'])]
    public function newForSet(Set $set, Request $request, EntityManagerInterface $entityManager): Response
    {
        $missingPart = new MissingPart();
        $form = $this->createForm(MissingPartType::class, $missingPart, [
            'set' => $set
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($missingPart);
            $entityManager->flush();

            $this->addFlash('success', 'Missing part added successfully!');
            return $this->redirectToRoute('app_set_show', ['id' => $set->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('missing_part/new_for_set.html.twig', [
            'missing_part' => $missingPart,
            'set' => $set,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_missing_part_show', methods: ['GET'])]
    public function show(MissingPart $missingPart): Response
    {
        return $this->render('missing_part/show.html.twig', [
            'missing_part' => $missingPart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_missing_part_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MissingPart $missingPart, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MissingPartType::class, $missingPart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_missing_part_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('missing_part/edit.html.twig', [
            'missing_part' => $missingPart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_missing_part_delete', methods: ['POST'])]
    public function delete(Request $request, MissingPart $missingPart, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$missingPart->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($missingPart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_missing_part_index', [], Response::HTTP_SEE_OTHER);
    }
}
