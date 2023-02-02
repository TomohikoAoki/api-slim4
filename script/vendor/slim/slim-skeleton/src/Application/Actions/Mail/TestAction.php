<?php

declare(strict_types=1);

namespace App\Application\Actions\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class TestAction
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

       $param = $request->getServerParams();
       $body = (array)$request->getParsedBody();

       $logger->info(print_r($param['HTTP_USER_AGENT'], true));
       $logger->info(print_r($body['zipCode'], true));

       return $response;
    }
}