<?php

declare(strict_types=1);

use App\Application\Actions\News;
use App\Application\Actions\Mail;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\FormValidationMiddleware;
use App\Application\Middleware\ImageValidationMiddleware;
use App\Application\Middleware\AuthorizationMiddleware;
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
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/posts', function (Group $group) {
        $group->get("/current", News\NewsListOfCurrentAction::class);
        $group->get("/{page:[0-9]+}", News\NewsListOfPageAction::class);
    });

    $app->get("/articles/{id}", News\ShowArticleAction::class);

    //メール送信
    $app->post('/mailer', Mail\SendMailAction::class);

    //imageのみ別個でアップロード
    $app->post('/image-upload', News\UploadImageAction::class)->add(ImageValidationMiddleware::class)->add(AuthorizationMiddleware::class)->add(JwtAuthentication::class);

    

    $app->group('/article', function (Group $group) {
        $group->post('/create', News\CreateNewsAction::class);
        $group->put('/edit/{id}', News\EditNewsAction::class);
        $group->delete('/delete/{id}', News\DeleteNewsAction::class);
    })->add(FormValidationMiddleware::class)->add(AuthorizationMiddleware::class)->add(JwtAuthentication::class);



    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });
};
