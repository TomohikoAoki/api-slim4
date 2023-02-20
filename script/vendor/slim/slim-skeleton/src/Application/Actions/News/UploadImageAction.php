<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

class UploadImageAction
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

        if (!empty($_FILES)) {
            $uploadedFiles = $request->getUpLoadedFiles();
            $uploadedFile = $uploadedFiles['thumb_nail'];

            //ファイル移動　＆ ファイルネーム登録
            if ($uploadedFile !== null && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile(PUBLIC_PATH . UPLOAD_PATH, $uploadedFile);
            }
        }

        $body = $response->getBody();
        $body->write($filename);

        return $response->withBody($body);

    }

    private function moveUploadedFile(string $directory, UploadedFileInterface $file)
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $file->moveTo($directory.DIRECTORY_SEPARATOR.$filename);

        return $filename;
    }
}
