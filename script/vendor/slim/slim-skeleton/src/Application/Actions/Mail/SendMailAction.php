<?php

declare(strict_types=1);

namespace App\Application\Actions\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Mail\MailService;
use Exception;

class SendMailAction
{

    public function __invoke(Request $request, Response $response): Response
    {
        $mail = new MailService();
        //メール送信
        try {
            $result = $mail->sendMail($request);

            if (!$result) {
                unset($_SESSION['formData']);
                $body = $response->getBody();
                $body->write('最初のメールでだめだった');

                return $response->withStatus(500)->withBody($body)->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            unset($_SESSION['formData']);
            $body = $response->getBody();
            $body->write(json_encode($e->getMessage()));

            return $response->withStatus(500)->withBody($body)->withHeader('Content-Type', 'application/json');
        }

        //返信メール　送信
        try {
            $result = $mail->sendMail($request, 'reply');

            if ($result) {
                unset($_SESSION['formData']);
                return $response->withStatus(200);
            }
            unset($_SESSION['formData']);
            $body = $response->getBody();
            $body->write('返信メールでだめだった');

            return $response->withStatus(500)->withBody($body)->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            unset($_SESSION['formData']);
            $body = $response->getBody();
            $body->write(json_encode($e->getMessage()));
            
            return $response->withStatus(500)->withBody($body)->withHeader('Content-Type', 'application/json');
        }
    }
}
