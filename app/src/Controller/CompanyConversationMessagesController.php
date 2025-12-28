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

final class CompanyConversationMessagesController
{
    #[Route(
        '/companies/{companyId}/conversations/{conversationId}/messages',
        name: 'company_conversation_messages',
        methods: ['GET']
    )]
    public function list(
        string $companyId,
        string $conversationId,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
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

        if (!Uuid::isValid($conversationId)) {
            return new JsonResponse(['error' => 'Invalid conversationId'], 400);
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

        // Access rule (MVP): only company owner can read
        if ($company->getOwner()->getId() !== $account->getId()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        /** @var Conversation|null $conversation */
        $conversation = $em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        // Scope rule: conversation must belong to this company
        if ($conversation->getCompany()->getId() !== $company->getId()) {
            return new JsonResponse(['error' => 'Conversation does not belong to company'], 404);
        }

        $messages = $em->getRepository(Message::class)->findBy(
            ['conversation' => $conversation],
            ['createdAt' => 'ASC']
        );

        $items = [];
        foreach ($messages as $m) {
            /** @var Message $m */
            $items[] = [
                'id' => $m->getId(),
                'direction' => $m->getDirection(),
                'text' => $m->getText(),
                'createdAt' => $m->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return new JsonResponse([
            'companyId' => $company->getId(),
            'conversationId' => $conversation->getId(),
            'messages' => $items,
        ]);
    }
}

