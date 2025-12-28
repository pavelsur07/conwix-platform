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

final class CompanyConversationReplyController
{
    #[Route(
        '/companies/{companyId}/conversations/{conversationId}/reply',
        name: 'company_conversation_reply',
        methods: ['POST']
    )]
    public function reply(
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

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !isset($data['text']) || !is_string($data['text']) || trim($data['text']) === '') {
            return new JsonResponse(['error' => 'text is required'], 400);
        }
        $text = trim($data['text']);

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

        if ($company->getOwner()->getId() !== $account->getId()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        /** @var Conversation|null $conversation */
        $conversation = $em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        if ($conversation->getCompany()->getId() !== $company->getId()) {
            return new JsonResponse(['error' => 'Conversation does not belong to company'], 404);
        }

        $message = new Message(
            Uuid::uuid4()->toString(),
            $conversation,
            Message::DIRECTION_OUT,
            $text,
            new \DateTimeImmutable()
        );

        $em->persist($message);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'companyId' => $company->getId(),
            'conversationId' => $conversation->getId(),
            'message' => [
                'id' => $message->getId(),
                'direction' => $message->getDirection(),
                'text' => $message->getText(),
                'createdAt' => $message->getCreatedAt()->format(DATE_ATOM),
            ],
        ], 201);
    }
}

