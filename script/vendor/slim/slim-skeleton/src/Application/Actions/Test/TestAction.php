<?php
declare(strict_types=1);

namespace App\Application\Actions\Test;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class TestAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $logger = $this->container->get(LoggerInterface::class);


        $query = $request->getQueryParams()['page'];

        $logger->info(print_r($query, true));

        $body = $response->getBody();
        $body->write('test'.$query);
        $response = $response->withBody($body)->withHeader('Content-Type','application/json');
        return $response;

    }
}