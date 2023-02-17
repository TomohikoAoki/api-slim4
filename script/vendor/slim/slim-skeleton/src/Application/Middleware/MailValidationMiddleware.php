<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;
use Valitron;

class MailValidationMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {

        //POST,PUTをバリデーション
        if (in_array($request->getMethod(), ['POST', 'PUT'])) {

            //前処理
            //フォームデータ取得
            $parameters = $request->getParsedBody();


            //バリデーターインスタンス
            Valitron\Validator::lang("ja");
            $validator = new Valitron\Validator($parameters);

            //ルール
            $validator->rule('required', ['lastName', 'firstName', 'email', 'emailConfirmed', 'shop', 'content'])->message('{field}は必須項目です。');
            $validator->rule('lengthMax', ['lastName', 'firstName'], 10)->message('入力可能な文字数は10字までです。');
            $validator->rule('email', 'email')->message('正しいメール形式で入力してください。');
            $validator->rule('equals', 'email', 'emailConfirmed')->message('メールアドレスと確認用メールアドレスが違います。');
            $validator->rule('regex', 'phoneNumber', '/^0[0-9]{9,10}$/')->message('ハイフンを入れず数字９桁or10桁の半角数字で市外局番から入力してください。');
            $validator->rule('regex', 'zipCode', '/^[0-9]{7}$/')->message('ハイフンを入れず数字7桁の半角数字で入力してください。');

            //ラベル
            $validator->labels([
                'lastName' => '氏名',
                'firstName' => '名前',
                'email' => 'メールアドレス',
                'emailConfirmed' => '確認用メールアドレス',
                'zipCode' => '郵便番号',
                'phoneNumber' => '電話番号',
                'shop' => '店舗',
                'content' => 'お問い合わせ内容'
            ]);

            if (!$validator->validate()) {
                //エラー時
                $message = $validator->errors();

                $response = (new ResponseFactory)->createResponse(422);

                $token['csrf_name'] = $request->getAttribute('csrf_name');
                $token['csrf_value'] = $request->getAttribute('csrf_value');

                $response
                    ->withHeader('Content-Type', 'application/json;charset=utf-8')
                    ->getBody()->write(json_encode(
                        ['messages' => $message, 'token' => $token],
                        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    ));

                return $response;
            }
        }

        return $handler->handle($request);
    }
}
