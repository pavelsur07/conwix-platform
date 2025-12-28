<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyListController
{
    #[Route('/companies', name: 'company_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $accountId = $request->headers->get('X-Account-Id');

        if (!is_string($accountId) || trim($accountId) === '') {
            return new JsonResponse(['error' => 'Missing X-Account-Id header'], 400);
        }

        $accountId = trim($accountId);
        if (!Uuid::isValid($accountId)) {
            return new JsonResponse(['error' => 'Invalid X-Account-Id'], 400);
        }

        $companies = $em->getRepository(Company::class)->findBy(
            ['owner' => $accountId],
            ['createdAt' => 'ASC']
        );

        $items = [];
        foreach ($companies as $c) {
            /** @var Company $c */
            $items[] = [
                'id' => $c->getId(),
                'name' => $c->getName(),
                'createdAt' => $c->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return new JsonResponse([
            'companies' => $items,
        ]);
    }
}
