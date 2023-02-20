<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class NewsListOfCurrentByShopAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $shopId = (int) $this->resolveArg('shopId');

        $this->container->get('db');
        $news = $this->newsRepository->findCurrentByShop($shopId);

        return $this->respondWithData($news);
    }
}