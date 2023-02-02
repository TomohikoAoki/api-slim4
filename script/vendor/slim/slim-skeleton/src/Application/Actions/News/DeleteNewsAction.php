<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteNewsAction extends NewsAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        if (!in_array('write', $request->getAttribute('user_auth'))) {
            throw new HttpUnauthorizedException($request);
        };

        $result = $this->newsRepository->delete($id);

        $body = $response->getBody();
        $body->write(json_encode($result));

        return $response->withBody($body);
        
    }
}