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
        $mail = new MailService();

        $logger = $this->container->get(LoggerInterface::class);

        try {
            $result = $mail->sendMail($request);

            if ($result) {
                $logger->info('成功');
                return $response->withStatus(200);
            }

            $logger->info('失敗');
            return $response->withStatus(200);
            
        } catch(Exception $e) {
            $logger->info('エラー発生');
            return $response;
        }
   
    }
}
