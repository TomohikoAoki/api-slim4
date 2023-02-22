<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\News\DeleteUploadedImageService;
use Slim\Exception\HttpUnauthorizedException;

class DeleteImageAction
{
    /**
     * @param Request $request
     * @param Response $response
     * 
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        if (!in_array('write', $request->getAttribute('user_auth'))) {
            throw new HttpUnauthorizedException($request);
        };

        $postBody = (array) $request->getParsedBody();
        $fileName = $postBody['file_name'];

        $service = new DeleteUploadedImageService();

        $service->deleteImage($fileName);

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
