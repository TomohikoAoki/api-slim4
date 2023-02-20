<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;

class EditNewsAction extends NewsAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $parameters = (array) $this->request->getParsedBody();

        $id = (int) $this->resolveArg('id');

        $parameters['shop_ids'] = json_encode($parameters['shop_ids']);

        if ($this->checkAuth('write')) {
            $this->container->get('db');
            $result = $this->newsRepository->update($parameters, $id);

            return $this->respondWithData($result);
        };

        
    }
}
