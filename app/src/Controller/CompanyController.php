<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController
{
    #[Route('/companies', name: 'company_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $accountId = $request->headers->get('X-Account-Id');

        if (!is_string($accountId) || trim($accountId) === '') {
            return new JsonResponse(['error' => 'Missing X-Account-Id header'], 400);
        }

        $accountId = trim($accountId);
        if (!Uuid::isValid($accountId)) {
            return new JsonResponse(['error' => 'Invalid X-Account-Id'], 400);
        }

        /** @var Account|null $owner */
        $owner = $em->getRepository(Account::class)->find($accountId);
        if (!$owner) {
            return new JsonResponse(['error' => 'Account not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || empty($data['name']) || !is_string($data['name'])) {
            return new JsonResponse(['error' => 'Company name is required'], 400);
        }

        $name = trim($data['name']);
        if ($name === '') {
            return new JsonResponse(['error' => 'Company name is required'], 400);
        }

        $company = new Company(
            Uuid::uuid4()->toString(),
            $owner,
            $name,
            new \DateTimeImmutable()
        );

        $em->persist($company);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'company' => [
                'id' => $company->getId(),
                'name' => $company->getName(),
                'createdAt' => $company->getCreatedAt()->format(DATE_ATOM),
                'ownerAccountId' => $company->getOwner()->getId(),
            ],
        ], 201);
    }
}
