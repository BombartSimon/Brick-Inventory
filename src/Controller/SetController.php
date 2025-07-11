<?php

namespace App\Controller;

use App\Entity\Set;
use App\Entity\SetPart;
use App\Form\SetType;
use App\Repository\MissingPartRepository;
use App\Repository\SetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\RebrickableClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class DashboardController extends AbstractController
{
    #[Route(name: 'app_dashboard', methods: ['GET'])]
    public function dashboard(): RedirectResponse
    {
        return $this->redirectToRoute('app_set_index');
    }
}

#[Route('/set')]
final class SetController extends AbstractController
{
    // ======= Crud Operations for Set Entity =======
    #[Route(name: 'app_set_index', methods: ['GET'])]
    public function index(Request $request, SetRepository $setRepository): Response
    {
        $search = $request->query->get('search', '');
        
        if ($search) {
            $sets = $setRepository->createQueryBuilder('s')
                ->where('LOWER(s.name) LIKE LOWER(:search) OR LOWER(s.setNum) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('s.importedAt', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $sets = $setRepository->findBy([], ['importedAt' => 'DESC']);
        }
        
        return $this->render('set/index.html.twig', [
            'sets' => $sets,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_set_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $set = new Set();
        $form = $this->createForm(SetType::class, $set);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($set);
            $entityManager->flush();

            return $this->redirectToRoute('app_set_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('set/new.html.twig', [
            'set' => $set,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_set_show', methods: ['GET'])]
    public function show(Set $set, MissingPartRepository $missingPartRepository): Response
    {
        // Get missing parts for this set
        $missingParts = $missingPartRepository->createQueryBuilder('mp')
            ->join('mp.part', 'sp')
            ->where('sp.set = :set')
            ->setParameter('set', $set)
            ->getQuery()
            ->getResult();

        return $this->render('set/show.html.twig', [
            'set' => $set,
            'set_parts' => $set->getSetParts(),
            'missing_parts' => $missingParts,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_set_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Set $set, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SetType::class, $set);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_set_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('set/edit.html.twig', [
            'set' => $set,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_set_delete', methods: ['POST'])]
    public function delete(Request $request, Set $set, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $set->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($set);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_set_index', [], Response::HTTP_SEE_OTHER);
    }

    // ============ Rebrickable API Integration ============
    #[Route('/import/{setNum}', name: 'app_set_import', methods: ['GET'])]
    public function importSet(
        string $setNum,
        RebrickableClient $rebrickableClient,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $data = $rebrickableClient->getSetInfo($setNum);

        $set = new Set();
        $set->setSetNum($data['set_num']);
        $set->setName($data['name']);
        $set->setYear($data['year']);
        $set->setImageUrl($data['set_img_url']);

        $set->setImportedAt(new \DateTimeImmutable());

        $entityManager->persist($set);

        $parts = $rebrickableClient->getSetParts($setNum);
        foreach ($parts['results'] as $part) {
            $setPart = new SetPart();

            $setPart->setSet($set);
            $setPart->setPartNum($part['part']['part_num']);
            $setPart->setPartName($part['part']['name']);
            $setPart->setColorId($part['color']['id']);
            $setPart->setQuantity($part['quantity']);
            $setPart->setImageUrl($part['part']['part_img_url']);

            $entityManager->persist($setPart);
        }
        $entityManager->flush();
        return $this->redirectToRoute('app_set_index');
    }
}
