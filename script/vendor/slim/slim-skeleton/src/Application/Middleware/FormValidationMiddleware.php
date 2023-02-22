<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Tuupola\Http\Factory\ResponseFactory;
use Valitron;

class FormValidationMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        //バリデーション設定
        $validationFormat = [
            //タイトルのマックス文字数
            'title' => [
                'lengthMax' => 10,
            ],
            //店舗ID
            'shop_ids' => [
                'in' => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            //店舗ID
            'public' => [
                'in' => [0, 1]
            ]

        ];


        //POST,PUTをバリデーション
        if (in_array($request->getMethod(), ['POST', 'PUT'])) {

            //前処理
            //フォームデータ取得
            $parameters = $request->getParsedBody();

            if (isset($parameters['shop_ids'])) {
                $parameters['shop_ids'] = $parameters['shop_ids'];
            }


            //バリデーターインスタンス
            Valitron\Validator::lang("ja");
            $validator = new Valitron\Validator($parameters);

            //ルール
            $validator->rule('required', ['title', 'content', 'public'])->message('{field}は必須項目です');
            $validator->rule('lengthMax', ['title'], $validationFormat['title']['lengthMax'])->message('{field}は30文字までです');
            $validator->rule('integer', ['public'])->message('{field}で不正な入力があります');
            $validator->rule('in', ['public'], $validationFormat['public']['in'])->message('{field}で不正な入力があります');
            $validator->rule('in', ['shop_ids.*'], $validationFormat['shop_ids']['in'])->message('{field}で不正な入力があります');

            //ラベル
            $validator->labels([
                'title' => 'タイトル',
                'public' => 'トップに公開',
                'shop_ids' => '店舗ページで公開',
                'content' => '内容',
            ]);

            if (!$validator->validate()) {
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
        }

        return $handler->handle($request);
    }
}
