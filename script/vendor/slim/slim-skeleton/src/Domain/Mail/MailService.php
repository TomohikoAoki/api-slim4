<?php

declare(strict_types=1);

namespace App\Domain\Mail;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPMailer\PHPMailer\PHPMailer;
use App\Domain\Mail\PostEditService;

class MailService
{

    /**
     * @var PHPMailer 
     */
    private $mailer;

    /**
     * @var array
     */
    private $options;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->options = [
            'Encoding' => 'base64',
            'CharSet' => 'UTF-8',
            'Host' => $_ENV["MAIL_HOST"],
            'SMTPAuth' => true,
            'Username' => $_ENV["MAIL_USERNAME"],
            'Password' => $_ENV["MAIL_PASSWORD"],
            'SMTPSecure' => 'tls',
            'Port' => 587,
        ];
    }

    public function sendMail(Request $request, string $mode = null)
    {
        mb_internal_encoding("UTF-8");
        $this->mailer->clearAddresses();

        $body = new PostEditService($request);

        try {
            $this->mailer->isSMTP();
            $this->mailer->setFrom($_ENV["MAIL_FROM_ADDRESS"], 'とんきゅう株式会社 本部');
            $this->mailer->isHTML(false);
            foreach ($this->options as $key => $value) {
                $this->mailer->{$key} = $value;
            }
            if (!$mode) {
                $this->mailer->addAddress('aoki@ton-q.com');
                $this->mailer->Subject = mb_encode_mimeheader('メールフォームからお問い合わせがありました。', 'ISO-2022-JP');
                $this->mailer->Body = $body->getMailBody();
            } else {
                $this->mailer->addAddress($body->getReplyAddress());
                $this->mailer->Subject = mb_encode_mimeheader('お問い合わせありがとうございました。', 'ISO-2022-JP');
                $this->mailer->Body = $body->getReplyMailBody();
            }

            return $this->mailer->send();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
