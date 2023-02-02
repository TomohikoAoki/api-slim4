<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Tuupola\Http\Factory\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\UploadedFileInterface;
use Valitron;

class ImageValidationMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        //バリデーション設定
        $validationFormat = [
            'image' => [
                'type' => ['image/jpeg', 'image/png', 'image/gif'],
                'capacity' => 100000,
                'size' => ['width' => 500, 'height' => 500]
            ]
        ];

        $parameters = null;

        //アップロードファイル取得
        if (!empty($_FILES)) {
            $uploadedFile = $request->getUpLoadedFiles();

            if (!isset($uploadedFile['thumb_nail']) || !$uploadedFile['thumb_nail'] instanceof UploadedFileInterface || !$uploadedFile['thumb_nail']->getError()) {
                //Psrのリクエスト介さず直接一時ファイルから画像サイズ,mime情報　取得
                $parameters['thumb_nail'] = getimagesize($_FILES['thumb_nail']['tmp_name']);
                $parameters['thumb_nail']['capacity'] = $uploadedFile['thumb_nail']->getSize();
            }
        }

        //バリデーション
        //カスタムルール追加　画像の容量
        Valitron\Validator::addRule(
            'imgCapacity',
            function ($field, $value, $param) {
                return ($value['capacity'] < $param[0]) ? true : false;
            }
        );
        //カスタムルール追加　画像サイズ
        Valitron\Validator::addRule(
            'imgSize',
            function ($field, $value, $params) {
                return ($value[0] < $params[0]['width'] && $value[1] < $params[0]['height']) ? true : false;
            }
        );
        //カスタムルール追加　画像タイプ
        Valitron\Validator::addRule(
            'imgType',
            function ($field, $value, $params) {
                return in_array($value['mime'], $params[0]) ? true : false;
            }
        );

        //バリデーターインスタンス
        Valitron\Validator::lang("ja");
        $validator = new Valitron\Validator($parameters);

        $validator->rule('imgType', ['thumb_nail'], $validationFormat['image']['type'])->message('{field}のファイル形式はjpeg,gif,pngです。');
        $validator->rule('imgCapacity', ['thumb_nail'], $validationFormat['image']['capacity'])->message('{field}のサイズが大きすぎます。');
        $validator->rule('imgSize', ['thumb_nail'], $validationFormat['image']['size'])->message('{field}は縦横500px以内のサイズにしてください。');

        //ラベル
        $validator->labels([
            'thumb_nail' => 'サムネイル画像',
        ]);

        if (!$validator->validate())  {
            //エラー時
            $message = $validator->errors();

            $response = (new ResponseFactory)->createResponse(422);

            $response
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->getBody()->write(json_encode(
                $message,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ));

            return $response;
        }

        return $handler->handle($request);
    }
}
