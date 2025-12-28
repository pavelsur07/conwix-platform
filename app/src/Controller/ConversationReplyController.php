<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ConversationReplyController
{
    #[Route('/conversations/{conversationId}/reply', name: 'conversation_reply', methods: ['POST'])]
    public function reply(string $conversationId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $accountId = $request->headers->get('X-Account-Id');
        if (!is_string($accountId) || trim($accountId) === '') {
            return new JsonResponse(['error' => 'Missing X-Account-Id header'], 400);
        }
        $accountId = trim($accountId);
        if (!Uuid::isValid($accountId)) {
            return new JsonResponse(['error' => 'Invalid X-Account-Id'], 400);
        }

        if (!Uuid::isValid($conversationId)) {
            return new JsonResponse(['error' => 'Invalid conversationId'], 400);
        }

        /** @var Conversation|null $conversation */
        $conversation = $em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        // Access rule (MVP): only company owner can reply
        $owner = $conversation->getCompany()->getOwner();
        if ($owner->getId() !== $accountId) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !isset($data['text']) || !is_string($data['text']) || trim($data['text']) === '') {
            return new JsonResponse(['error' => 'text is required'], 400);
        }

        $message = new Message(
            \Ramsey\Uuid\Uuid::uuid4()->toString(),
            $conversation,
            Message::DIRECTION_OUT,
            trim($data['text']),
            new \DateTimeImmutable()
        );

        $em->persist($message);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'message' => [
                'id' => $message->getId(),
                'direction' => $message->getDirection(),
                'createdAt' => $message->getCreatedAt()->format(DATE_ATOM),
            ],
        ], 201);
    }
}

