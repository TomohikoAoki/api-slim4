<?php

declare(strict_types=1);

namespace App\Application\Actions\News;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class CreateNewsAction extends NewsAction
{
    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $parameters = (array)$request->getParsedBody();

        $parameters['shop_ids'] = json_encode($parameters['shop_ids']);

        if (!in_array('write', $request->getAttribute('user_auth'))) {
            throw new HttpUnauthorizedException($request);
        };

        //データベースに登録
        try {
            $result = $this->newsRepository->register($parameters);
        } catch (Exception $e) {
            throw new Exception('クリエイトなぜかだめ');
        };


        $body = $response->getBody();
        $body->write(json_encode($result));

        return $response->withHeader('Context-Type', 'application/json');
    }
}
