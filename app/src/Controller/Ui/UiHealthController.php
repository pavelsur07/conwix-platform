<?php

declare(strict_types=1);

namespace App\Controller\Ui;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UiHealthController
{
    #[Route('/ui/health', name: 'ui_health', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $accountId = (string) $request->headers->get('X-Account-Id', '');
        if ($accountId === '') {
            return new Response('Missing X-Account-Id header', 400, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        $body = "UI Health: OK\n";
        $body .= "Time (UTC): ".gmdate('c')."\n";
        $body .= "X-Account-Id: ".$accountId."\n";
        $body .= "PHP: ".PHP_VERSION."\n";

        return new Response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
