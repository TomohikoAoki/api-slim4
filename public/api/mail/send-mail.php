<?php

session_start();
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type,Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');


$csrfCookieName = 'X-XSRF-TOKEN';
$varified = !empty($_SESSION['csrfToken']) &&
    !empty($_COOKIE[$csrfCookieName]) &&
    ($_SESSION['csrfToken'] === $_COOKIE[$csrfCookieName]);

    
//SESSIONのトークンとcookieのトークンが存在して合っていなければ
if (!$varified) {
    $csrfToken = rtrim(base64_encode(openssl_random_pseudo_bytes(32)), '=');
    setcookie($csrfCookieName, $csrfToken,0,"/");
    $_SESSION['csrfToken'] = $csrfToken;
    
    echo 'トークン発行';
    exit;
}

//SESSIONのトークンとcookieのトークンが存在して合っていれば
//メーラー本体呼び出し

echo 'トークン発行済み';
exit;
require '../../script/sendmail/mailer.php';




