<?php

declare(strict_types=1);

$publicUrl = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . getBasePath();

// public index.php path
define('PUBLIC_PATH', __DIR__);
define('PUBLIC_URI', $publicUrl);
define('UPLOAD_PATH', "/cache/uploads");

// app path
require_once __DIR__ . '/../../script/vendor/slim/slim-skeleton/path.php';

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Factory\ServerRequestCreatorFactory;

require APP_PATH . '/../../autoload.php';

//.envを取得
$dotenv = Dotenv\Dotenv::createImmutable((APP_PATH));
$dotenv->load();

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
    $containerBuilder->enableCompilation(APP_PATH . '/var/cache');
}

// Set up settings
$settings = require  APP_PATH . '/app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require APP_PATH . '/app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require APP_PATH . '/app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

$app->setBasePath(getBasePath());

// Register middleware
$middleware = require APP_PATH . '/app/middleware.php';
$middleware($app);

// Register routes
$routes = require APP_PATH . '/app/routes.php';
$routes($app);


/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$logger = $container->get(LoggerInterface::class);
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory, $logger);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

//for POSTed JSON or XML data parsing
$app->addBodyParsingMiddleware();

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);

//Get Base Path
function getBasePath()
{
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($basePath == '/') return '';
    return $basePath;
}
