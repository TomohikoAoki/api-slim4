<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class ShowArticleAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = (int) $this->resolveArg('id');
        $this->container->get('db');
        $article = $this->newsRepository->findId($id);

        return $this->respondWithData($article);
    }
}