<?php

declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class NewsListOfPageAction extends NewsAction
{

    /**
     * {@inheritdoc}
     */
    public function action(): Response 
    {
        $page = (int) $this->resolveQuery('page');
        $limit = 7;

        $this->container->get('db');

        $count = $this->newsRepository->countAll();
        $news = $this->newsRepository->findAllWithPage($limit, $page);

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
