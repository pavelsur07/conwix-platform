<?php

declare(strict_types=1);

namespace App\Controller\Ui;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final class UiCompanyController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/ui/companies', name: 'ui_companies', methods: ['GET'])]
    public function companies(Request $request): Response
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

        // MVP: если у тебя есть связь Company->ownerAccountId — можно фильтровать.
        // Сейчас — просто показываем все компании (как и в твоём API list).
        $companies = $this->em->getRepository(Company::class)->findBy([], ['createdAt' => 'DESC']);

        return new Response($this->twig->render('ui/companies.html.twig', [
            'accountId' => $accountId,
            'companies' => $companies,
        ]));
    }
}

