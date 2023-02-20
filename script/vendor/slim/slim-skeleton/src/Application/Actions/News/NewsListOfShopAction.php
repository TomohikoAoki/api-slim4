<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class NewsListOfShopAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $page = (int) $this->resolvePageQuery();
        $shopId = (int) $this->resolveArg('shopId');
        $limit = 7;

        $this->container->get('db');
        $count = $this->newsRepository->countByShop($shopId);
        $news = $this->newsRepository->findByShopId($shopId, $limit, $page);

        $data = [
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $count,
            ],
            'news_data' => $news
        ];

        return $this->respondWithData($data);
    }
}
