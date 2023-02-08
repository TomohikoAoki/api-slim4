<?php

declare(strict_types=1);

use App\Application\Actions\News;
use App\Application\Actions\Mail;
use App\Application\Actions\Test;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\FormValidationMiddleware;
use App\Application\Middleware\ImageValidationMiddleware;
use App\Application\Middleware\AuthorizationMiddleware;
use App\Application\Middleware\MailValidationMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\Csrf;
use Tuupola\Middleware\JwtAuthentication;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('/', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    //テスト
    $app->get('/test', Test\TestAction::class);

    //ニュース記事取得
    $app->group('/posts', function (Group $group) {
        $group->get('/index', News\NewsListOfPageAction::class);
        $group->get("/current", News\NewsListOfCurrentAction::class);
        $group->get("/shop/{shopId}", News\NewsListOfShopAction::class);
    });

    $app->get("/articles/{id}", News\ShowArticleAction::class);

    //メール送信
    $app->group('/mail', function (Group $group) {
        $group->get('/token', Mail\IssueTokenAction::class);
        $group->post('/check', Mail\CheckMailAction::class)->add(MailValidationMiddleware::class);
        $group->post('/send', Mail\SendMailAction::class);
    })->add(Csrf\Guard::class)->add(SessionMiddleware::class);

    //ニュース記事　フォームでの編集
    $app->group('/article', function (Group $group) {
        $group->post('/create', News\CreateNewsAction::class)->add(FormValidationMiddleware::class);
        $group->put('/edit/{id}', News\EditNewsAction::class)->add(FormValidationMiddleware::class);
        $group->post('/image-upload', News\UploadImageAction::class)->add(ImageValidationMiddleware::class);
        $group->delete('/delete/{id}', News\DeleteNewsAction::class);
    })->add(AuthorizationMiddleware::class)->add(JwtAuthentication::class);


    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });
};
