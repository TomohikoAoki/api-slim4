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

    public function sendMail(Request $request)
    {
        mb_internal_encoding("UTF-8");

        try {
            $this->mailer->isSMTP();
            foreach ($this->options as $key => $value) {
                $this->mailer->{$key} = $value;
            }
            $this->mailer->setFrom($_ENV["MAIL_FROM_ADDRESS"], 'とんきゅう株式会社 本部');
            $this->mailer->addAddress('aoki@ton-q.com', '青木智彦');
            $this->mailer->Subject = mb_encode_mimeheader('メールフォームからお問い合わせがありました。', 'ISO-2022-JP');
            $this->mailer->isHTML(false);

            $body = new PostEditService($request);

            $this->mailer->Body = $body->getMailBody();

            return $this->mailer->send();
            
        } catch (Exception $e) {
            throw $e;
        }
    }

}
