<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteNewsAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $id = (int) $this->resolveArg('id');

        if($this->checkAuth('write')) {
            $this->container->get('db');
            $result = $this->newsRepository->delete($id);

            return $this->respondWithData($result);
        }
    }
}