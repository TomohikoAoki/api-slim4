<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class NewsListOfPageAction extends NewsAction
{
    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $page = ($args['page'] !== null || $args['page'] > 0) ? (int)$args['page'] : 1;
        $limit = 7;

        try {
            $this->container->get('db');
            $count = $this->newsRepository->countAll();
            $news = $this->newsRepository->findAllWithPage($limit, $page);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        };

        $data = [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'news_data' => $news
        ];

        $body = $response->getBody();
        $body->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        $response = $response->withBody($body)->withHeader('Content-Type','application/json');
        return $response;

    }
}