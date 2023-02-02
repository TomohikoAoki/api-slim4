<?php
declare(strict_types=1);

use App\Domain\News\NewsRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\News\ExposedNewsRepository;
use App\Infrastructure\Persistence\User\ExposedUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(ExposedUserRepository::class),
        NewsRepository::class => \DI\autowire(ExposedNewsRepository::class),
    ]);
};
