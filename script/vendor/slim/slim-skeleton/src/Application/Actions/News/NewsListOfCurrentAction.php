<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class NewsListOfCurrentAction extends NewsAction
{
    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {

        try {
            $news = $this->newsRepository->findCurrent();
        } catch(Exception $e) {
            throw new Exception();
        };

        $body = $response->getBody();
        $body->write(json_encode($news, JSON_UNESCAPED_UNICODE));
        $response = $response->withBody($body)->withHeader('Content-Type','application/json');
        return $response;

    }
}