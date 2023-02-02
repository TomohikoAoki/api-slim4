<?php

declare(strict_types=1);

namespace App\Application\Actions\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class SendMailAction
{

    public function __invoke(Request $request, Response $response): Response
    {
        mb_internal_encoding("UTF-8");
        $mail = new PHPMailer(true);

        $mailBody = 'test';

        //日本語用設定
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";

        try {
            //サーバの設定
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // デバグの出力を有効に（テスト環境での検証用）
            $mail->isSMTP();   // SMTP を使用
            $mail->Host       = $_ENV["MAIL_HOST"];  // SMTP サーバーを指定
            $mail->SMTPAuth   = true;   // SMTP authentication を有効に
            $mail->Username   = $_ENV["MAIL_USERNAME"];  // SMTP ユーザ名
            $mail->Password   = $_ENV["MAIL_PASSWORD"];  // SMTP パスワード
            $mail->SMTPSecure = 'tls';  // 暗号化を有効に
            $mail->Port       = 587;  // TCP ポートを指定

            //※名前などに日本語を使う場合は文字エンコーディングを変換
            //差出人アドレス, 差出人名
            $mail->setFrom($_ENV["MAIL_FROM_ADDRESS"], 'とんきゅう株式会社 本部');
            $mail->addAddress('aoki@ton-q.com', '青木智彦');
            $mail->Subject = mb_encode_mimeheader('メールフォームからお問い合わせがありました。', 'ISO-2022-JP');
            $mail->isHTML(false);
            $mail->Body = $mailBody;

            //メッセージ送信
            if (!$mail->send()) {
                //エラーの場合
                exit;
            } else {
                return $response;
            }
        } catch (Exception $e) {
            //エラー（例外：Exception）が発生した場合

        }
        return $response;
    }
}
