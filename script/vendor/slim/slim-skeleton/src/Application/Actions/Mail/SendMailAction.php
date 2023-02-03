<?php

declare(strict_types=1);

namespace App\Application\Actions\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Mail\MailService;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class SendMailAction
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $logger = $this->container->get(LoggerInterface::class);

        $mail = new MailService();
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
            $body->write('最初のメールでだめだった,catchで');

            return $response->withStatus(500)->withBody($body)->withHeader('Content-Type', 'application/json');
        }

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
            
            $logger->info(print_r($e->getMessage(),true));


            return $response->withStatus(500)->withBody($body)->withHeader('Content-Type', 'application/json');
        }
    }
}
