<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class NewsListOfCurrentAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $this->container->get('db');
        $news = $this->newsRepository->findCurrent();

        return $this->respondWithData($news);
    }
}