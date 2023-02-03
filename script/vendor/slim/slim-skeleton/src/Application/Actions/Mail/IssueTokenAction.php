<?php

declare(strict_types=1);

namespace App\Application\Actions\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class IssueTokenAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $name = $request->getAttribute('csrf_name');
        $value = $request->getAttribute('csrf_value');

        $body = $response->getBody();
        $body->write(json_encode([
            'csrf_name' => $name,
            'csrf_value' => $value
        ], JSON_UNESCAPED_UNICODE));
        $response = $response->withBody($body)->withHeader('Content-Type', 'application/json');

        return $response;
    }
}
