<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ConversationMessagesController
{
    #[Route('/conversations/{conversationId}/messages', name: 'conversation_messages', methods: ['GET'])]
    public function list(string $conversationId, Request $request, EntityManagerInterface $em): JsonResponse
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

        // Access rule (MVP): only company owner can read messages
        $owner = $conversation->getCompany()->getOwner();
        if ($owner->getId() !== $accountId) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
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
            'conversationId' => $conversation->getId(),
            'messages' => $items,
        ]);
    }
}
