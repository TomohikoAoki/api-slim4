<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Csrf\Guard;
use Illuminate\Database\Capsule\Manager;
use Tuupola\Middleware\JwtAuthentication;
use App\Application\Helper\PublicKey;
use App\Application\Middleware\AuthorizationMiddleware;
use App\Domain\User\UserRepository;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        'db' => function (ContainerInterface $c) {

            $settings = $c->get(SettingsInterface::class);

            $dbSettings = $settings->get('db');

            $capsule = new Manager();
            $capsule->addConnection($dbSettings);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        },
        JwtAuthentication::class => static function (ContainerInterface $c): JwtAuthentication {
            $settings = $c->get(SettingsInterface::class);
            $jwtSettings = $settings->get('jwt_authentication');
            $jwtSettings['logger'] = $c->get(LoggerInterface::class);
            $jwtSettings['secret'] = PublicKey::getKey();
            return new JwtAuthentication($jwtSettings);
        },
        AuthorizationMiddleware::class => function (ContainerInterface $c): AuthorizationMiddleware {
            $settings = $c->get(SettingsInterface::class);
            $authSettings = $settings->get('authorization_middleware');
            return new AuthorizationMiddleware($c->get(UserRepository::class), $authSettings);
        },
        Guard::class => function (ContainerInterface $container) {
            $response = new ResponseFactory();
            return new Guard($response);
        },
    ]);
};
