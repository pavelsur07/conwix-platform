<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController
{
    #[Route('/register', name: 'account_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || empty($data['email']) || !is_string($data['email'])) {
            return new JsonResponse(['error' => 'Email is required'], 400);
        }

        $email = trim($data['email']);
        if ($email === '') {
            return new JsonResponse(['error' => 'Email is required'], 400);
        }

        $account = new Account(
            Uuid::uuid4()->toString(),
            $email,
            new \DateTimeImmutable()
        );

        $em->persist($account);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'account' => [
                'id' => $account->getId(),
                'email' => $account->getEmail(),
                'createdAt' => $account->getCreatedAt()->format(DATE_ATOM),
            ],
        ], 201);
    }

    #[Route('/me', name: 'account_me', methods: ['GET'])]
    public function me(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $accountId = $request->headers->get('X-Account-Id');

        if (!is_string($accountId) || trim($accountId) === '') {
            return new JsonResponse([
                'error' => 'Missing X-Account-Id header',
            ], 400);
        }

        $accountId = trim($accountId);
        if (!Uuid::isValid($accountId)) {
            return new JsonResponse([
                'error' => 'Invalid X-Account-Id',
            ], 400);
        }

        /** @var Account|null $account */
        $account = $em->getRepository(Account::class)->find($accountId);

        if (!$account) {
            return new JsonResponse(['error' => 'Account not found'], 404);
        }

        return new JsonResponse([
            'account' => [
                'id' => $account->getId(),
                'email' => $account->getEmail(),
                'createdAt' => $account->getCreatedAt()->format(DATE_ATOM),
            ],
        ]);
    }
}
