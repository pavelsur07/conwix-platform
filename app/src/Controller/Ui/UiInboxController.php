<?php

declare(strict_types=1);

namespace App\Controller\Ui;

use App\Entity\Company;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final class UiInboxController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/ui/companies/{companyId}/inbox', name: 'ui_inbox', methods: ['GET'])]
    public function inbox(string $companyId, Request $request): Response
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

        // MVP: список диалогов по компании
        $conversations = $this->em->getRepository(Conversation::class)->findBy(
            ['company' => $company],
            ['createdAt' => 'DESC']
        );

        return new Response($this->twig->render('ui/inbox.html.twig', [
            'accountId' => $accountId,
            'company' => $company,
            'conversations' => $conversations,
        ]));
    }
}
