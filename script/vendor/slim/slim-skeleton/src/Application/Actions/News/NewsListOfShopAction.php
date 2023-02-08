<?php

declare(strict_types=1);

namespace App\Application\Actions\News;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class NewsListOfShopAction extends NewsAction
{
    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $page = (int)$request->getQueryParams()['page'];
        $shopId = (int)$args['shopId'];
        $limit = 7;

        $logger = $this->container->get(LoggerInterface::class);

        try {
            $this->container->get('db');
            $count = $this->newsRepository->countByShop($shopId);
            $news = $this->newsRepository->findByShopId($shopId, $limit, $page);
        } catch (Exception $e) {
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
        $response = $response->withBody($body)->withHeader('Content-Type', 'application/json');
        return $response;
    }
}
