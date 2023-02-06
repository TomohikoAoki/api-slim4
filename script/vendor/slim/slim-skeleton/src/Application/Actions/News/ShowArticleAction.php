<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ShowArticleAction extends NewsAction
{

    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {

        $id = (int) $args['id'];

        try {
            $this->container->get('db');
            $article = $this->newsRepository->findId($id);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        };

        $body = $response->getBody();
        $body->write(json_encode($article, JSON_UNESCAPED_UNICODE));
        $response = $response->withBody($body)->withHeader('Content-Type','application/json');
        return $response;

    }
}