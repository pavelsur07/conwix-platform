<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyConversationsController
{
    #[Route('/companies/{companyId}/conversations', name: 'company_conversations', methods: ['GET'])]
    public function list(string $companyId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $accountId = $request->headers->get('X-Account-Id');

        if (!is_string($accountId) || trim($accountId) === '') {
            return new JsonResponse(['error' => 'Missing X-Account-Id header'], 400);
        }

        $accountId = trim($accountId);
        if (!Uuid::isValid($accountId)) {
            return new JsonResponse(['error' => 'Invalid X-Account-Id'], 400);
        }

        if (!Uuid::isValid($companyId)) {
            return new JsonResponse(['error' => 'Invalid companyId'], 400);
        }

        /** @var Account|null $account */
        $account = $em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            return new JsonResponse(['error' => 'Account not found'], 404);
        }

        /** @var Company|null $company */
        $company = $em->getRepository(Company::class)->find($companyId);
        if (!$company) {
            return new JsonResponse(['error' => 'Company not found'], 404);
        }

        // Access rule (MVP): only company owner can list conversations
        if ($company->getOwner()->getId() !== $account->getId()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $conversations = $em->getRepository(Conversation::class)->findBy(
            ['company' => $company],
            ['createdAt' => 'DESC']
        );

        $items = [];

        foreach ($conversations as $conv) {
            /** @var Conversation $conv */

            /** @var Message|null $last */
            $last = $em->getRepository(Message::class)->findOneBy(
                ['conversation' => $conv],
                ['createdAt' => 'DESC']
            );

            $items[] = [
                'id' => $conv->getId(),
                'clientExternalId' => $conv->getClient()->getExternalId(),
                'createdAt' => $conv->getCreatedAt()->format(DATE_ATOM),
                'lastMessage' => $last ? [
                    'id' => $last->getId(),
                    'direction' => $last->getDirection(),
                    'text' => $last->getText(),
                    'createdAt' => $last->getCreatedAt()->format(DATE_ATOM),
                ] : null,
            ];
        }

        return new JsonResponse([
            'companyId' => $company->getId(),
            'conversations' => $items,
        ]);
    }
}

