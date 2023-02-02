<?php

declare(strict_types=1);

namespace App\Application\Helper;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use GuzzleHttp\Client;
use Slim\Factory\AppFactory;
use Psr\Log\LoggerInterface;

class PublicKey
{
    public static function getKey()
    {
        $app = AppFactory::create();

        $container = $app->getContainer();

        $logger = $container->get(LoggerInterFace::class);

        CacheManager::setDefaultConfig(new ConfigurationOption([
            'path' => APP_PATH . "/tmp/auth/cache"
        ]));

        $InstanceCachePool = CacheManager::getInstance('files');

        $CacheKey = "public_key";
        $CachedItem = $InstanceCachePool->getItem($CacheKey);

        if (!$CachedItem->isHit()) {

            $logger->info('Useing public-key from http-request');

            $client = new Client();

            $response = $client->request('GET', $_ENV["AUTH_MIDDLEWARE_PUBLICKEY"]);

            $keys = $response->getBody()->getContents();

            $CachedItem->set($keys)->expiresAfter(60 * 60 * 1);
            $InstanceCachePool->save($CachedItem);

            return json_decode($keys, true);
        }

        $logger->info('Using public-key from cached');

        return json_decode($CachedItem->get(), true);
    }
}
