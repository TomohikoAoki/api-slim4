<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => true,
                'logErrorDetails'     => true,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'db' => [
                    'driver' => $_ENV["DB_DRIVER"],
                    'host' => $_ENV["DB_HOST"],
                    'database' => $_ENV["DB_DATABASE"],
                    'username' => $_ENV["DB_USERNAME"],
                    'password' => $_ENV["DB_PASSWORD"],
                    'charset'   => $_ENV["DB_CHARSET"],
                    'collation' => $_ENV["DB_COLLATION"],
                    'prefix'    => $_ENV["DB_PREFIX"],
                ],
                'jwt_authentication' => [
                    'secure' => false, // only for localhost for prod and test env set true
                    'error' => function ($response, $arguments) {
                        $data['status'] = 401;
                        $data['error'] = 'Unauthorized/' . $arguments['message'];
                        return $response
                            ->withHeader('Content-Type', 'application/json;charset=utf-8')
                            ->getBody()->write(json_encode(
                                $data,
                                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                            ));
                    },
                    'attribute' => 'payload',
                    'before' => function ($request, $params) {                        
                            return $request->withAttribute('token', $params['token']);
                    }
                ],
                'authorization_middleware' => [
                    'iss' => $_ENV["AUTH_MIDDLEWARE_ISS"],
                ]
            ]);
        }
    ]);
};
