<?php

declare(strict_types=1);

namespace App\Application\Actions\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CheckMailAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();

        foreach ($body as $key => $value) {
            if ($key === 'csrf_name' || $key === 'csrf_value' || $key === "emailConfirmed") {
                unset($body[$key]);
                continue;
            }
            $body[$key] = $this->sanitize($value);
        }

        $_SESSION['formData'] = $body;

        $token['csrf_name'] = $request->getAttribute('csrf_name');
        $token['csrf_value'] = $request->getAttribute('csrf_value');



        $resBody = $response->getBody();
        $resBody->write(json_encode(['body' => $body, 'token' => $token], JSON_UNESCAPED_UNICODE));

        $response = $response->withBody($resBody)->withHeader('Content-Type', 'application/json');

        return $response;
    }

    // テキスト消毒
    private function sanitize($p)
    {
        $p = htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
        str_replace(array("\r\n", "\r", "\n"), '', $p);

        return $p;
    }
}
