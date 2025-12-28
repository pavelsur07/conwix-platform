<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class IncomingMessageController
{
    #[Route('/events/incoming-message', name: 'incoming_message', methods: ['POST'])]
    public function incoming(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $companyId = $data['companyId'] ?? null;
        $externalId = $data['clientExternalId'] ?? null;
        $text = $data['text'] ?? null;

        if (!is_string($companyId) || trim($companyId) === '' || !Uuid::isValid($companyId)) {
            return new JsonResponse(['error' => 'companyId is required and must be uuid'], 400);
        }
        if (!is_string($externalId) || trim($externalId) === '') {
            return new JsonResponse(['error' => 'clientExternalId is required'], 400);
        }
        if (!is_string($text) || trim($text) === '') {
            return new JsonResponse(['error' => 'text is required'], 400);
        }

        /** @var Company|null $company */
        $company = $em->getRepository(Company::class)->find($companyId);
        if (!$company) {
            return new JsonResponse(['error' => 'Company not found'], 404);
        }

        $externalId = trim($externalId);

        /** @var Client|null $client */
        $client = $em->getRepository(Client::class)->findOneBy([
            'company' => $company,
            'externalId' => $externalId,
        ]);

        if (!$client) {
            $client = new Client(
                Uuid::uuid4()->toString(),
                $company,
                $externalId,
                new \DateTimeImmutable()
            );
            $em->persist($client);
        }

        /** @var Conversation|null $conversation */
        $conversation = $em->getRepository(Conversation::class)->findOneBy([
            'company' => $company,
            'client' => $client,
        ]);

        if (!$conversation) {
            $conversation = new Conversation(
                Uuid::uuid4()->toString(),
                $company,
                $client,
                new \DateTimeImmutable()
            );
            $em->persist($conversation);
        }

        $message = new Message(
            Uuid::uuid4()->toString(),
            $conversation,
            Message::DIRECTION_IN,
            trim($text),
            new \DateTimeImmutable()
        );

        $em->persist($message);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'conversationId' => $conversation->getId(),
            'message' => [
                'id' => $message->getId(),
                'direction' => $message->getDirection(),
                'createdAt' => $message->getCreatedAt()->format(DATE_ATOM),
            ],
        ], 201);
    }
}

