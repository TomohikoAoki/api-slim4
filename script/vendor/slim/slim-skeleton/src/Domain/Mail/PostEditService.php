<?php

declare(strict_types=1);

namespace App\Domain\Mail;

use Psr\Http\Message\ServerRequestInterface as Request;

class PostEditService
{
	/**
	 * @var array
	 */
	private $requestBody;

	/**
	 * @var array
	 */
	private $serverParams;

	/**
	 * @var array
	 */
	private $keyLabels = [
		'name' => '名前',
		'email' => 'メールアドレス',
		'gender' => '性別',
		'address' => '住所',
		'zipCode' => '郵便番号',
		'phoneNumber' => '電話番号',
		'shop' => '店舗',
		'content' => 'お問い合わせ内容'
	];

	public function __construct(Request $request)
	{
		$this->requestBody = $request->getParsedBody();
		$this->serverParams = $request->getServerParams();
	}

	public function getMailBody()
	{
		$set_body = PHP_EOL;

		$set_body .= <<<EOT

メールフォームからお問い合わせがありました。
お問い合わせの内容は以下の通りです。

EOT;

		$set_body .= $this->createBody($this->requestBody);
		$set_body .= $this->createBodyOfSenderInfo($this->serverParams);

		return $set_body;
	}
	

	private function createBodyOfSenderInfo(array $params)
	{
		$send_date = date('Y年m月d日　H時i分s秒');

		$remoteAddr = isset($params['REMOTE_ADDR']) ? $params['REMOTE_ADDR'] : '';
		$remoteHost = isset($params['REMOTE_HOST']) ? $params['REMOTE_HOST'] : '';
		$httpUserAgent = isset($params['HTTP_USER_AGENT']) ? $params['HTTP_USER_AGENT'] : '';

		$set_body  = PHP_EOL;
		$set_body .= '-----------------------------------------------------------------------------------' . PHP_EOL;
		$set_body .= PHP_EOL;
		$set_body .= '【送信時刻】' . PHP_EOL;
		$set_body .= $send_date;

		$set_body .= PHP_EOL;
		$set_body .= PHP_EOL;
		$set_body .= '-----------------------------------------------------------------------------------' . PHP_EOL;
		$set_body .= PHP_EOL;
		$set_body .= '【送信者のIPアドレス】' . PHP_EOL;
		$set_body .= $remoteAddr . '' . PHP_EOL;
		$set_body .= PHP_EOL;
		$set_body .= '【送信者のホスト名】' . PHP_EOL;
		$set_body .= $remoteHost . '' . PHP_EOL;
		$set_body .= PHP_EOL;
		$set_body .= '【送信者のブラウザ】' . PHP_EOL;
		$set_body .= $httpUserAgent . '' . PHP_EOL;
		$set_body .= PHP_EOL;

		return $set_body;
	}

	private function createBody(array $inputs)
	{
		$set_body = PHP_EOL;

		foreach ($inputs as $key => $value) {
			if ($value !== "") {
				$set_body .= PHP_EOL;

				$label = isset($this->keyLabels[$key]) ? $this->keyLabels[$key] : $key;

				$set_body .= '【' . $label . '】' . PHP_EOL;
				$set_body .= $this->sanitize($value);
				$set_body .= PHP_EOL;
			}
		}

		return $set_body;
	}

	private function sanitize($p)
	{
		$p = htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
		str_replace(array("\r\n", "\r", "\n"), '', $p);

		return $p;
	}
}
