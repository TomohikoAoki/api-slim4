<?php

declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class CreateNewsAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $parameters = (array) $this->request->getParsedBody();
        
        $parameters['shop_ids'] = json_encode($parameters['shop_ids']);

        if ($this->checkAuth('write')) {
            $this->container->get('db');
            $result = $this->newsRepository->register($parameters);

            return $this->respondWithData($result);
        };


    }
}
