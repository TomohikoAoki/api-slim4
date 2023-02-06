<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EditNewsAction extends NewsAction
{
    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $parameters = (array) $request->getParsedBody();

        $id = (int) $args['id'];

        $parameters['shop_ids'] = json_encode($parameters['shop_ids']);

        if (!in_array('write', $request->getAttribute('user_auth'))) {
            throw new HttpUnauthorizedException($request);
        };

        //データベース更新
        try {
            $this->container->get('db');
            $result = $this->newsRepository->update($parameters, $id);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
        

        $body = $response->getBody();
        $body->write(json_encode($result));
        
        return $response->withBody($body)->withHeader('Context-Type', 'application/json'); 

    }
}