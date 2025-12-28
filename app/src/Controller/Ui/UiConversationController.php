<?php

declare(strict_types=1);

namespace App\Controller\Ui;

use App\Entity\Company;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final class UiConversationController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/ui/companies/{companyId}/conversations/{conversationId}', name: 'ui_conversation', methods: ['GET'])]
    public function view(string $companyId, string $conversationId, Request $request): Response
    {
        $accountId = (string) $request->headers->get('X-Account-Id', '');
        if ($accountId === '') {
            return new Response(
                $this->twig->render('ui/error.html.twig', [
                    'title' => 'Missing header',
                    'message' => 'Provide X-Account-Id header to use UI in dev.',
                ]),
                400
            );
        }

        $company = $this->em->getRepository(Company::class)->find($companyId);
        if (!$company) {
            return new Response(
                $this->twig->render('ui/error.html.twig', [
                    'title' => 'Company not found',
                    'message' => 'Company does not exist: '.$companyId,
                ]),
                404
            );
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation || (string) $conversation->getCompany()->getId() !== (string) $company->getId()) {
            return new Response(
                $this->twig->render('ui/error.html.twig', [
                    'title' => 'Conversation not found',
                    'message' => 'Conversation does not exist in this company.',
                ]),
                404
            );
        }

        $messages = $this->em->getRepository(Message::class)->findBy(
            ['conversation' => $conversation],
            ['createdAt' => 'ASC']
        );

        return new Response($this->twig->render('ui/conversation.html.twig', [
            'accountId' => $accountId,
            'company' => $company,
            'conversation' => $conversation,
            'messages' => $messages,
        ]));
    }

    #[Route('/ui/companies/{companyId}/conversations/{conversationId}/reply', name: 'ui_conversation_reply', methods: ['POST'])]
    public function reply(string $companyId, string $conversationId, Request $request): Response
    {
        $accountId = (string) $request->headers->get('X-Account-Id', '');
        if ($accountId === '') {
            return new Response('Missing X-Account-Id', 400);
        }

        $text = trim((string) $request->request->get('text', ''));
        if ($text === '') {
            return new RedirectResponse("/ui/companies/{$companyId}/conversations/{$conversationId}", 302);
        }

        // Мы НЕ зовём HTTP API. Мы создаём исходящее сообщение напрямую (MVP).
        // Предполагаем, что Message умеет создаваться как "out" и привязывается к Conversation.
        $conversation = $this->em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new Response('Conversation not found', 404);
        }

        // Если у тебя фабрика/enum — подставь тут. Ниже — самый простой вариант.
        $message = Message::createOutgoing($conversation, $text);

        $this->em->persist($message);
        $this->em->flush();

        return new RedirectResponse("/ui/companies/{$companyId}/conversations/{$conversationId}", 302);
    }
}

