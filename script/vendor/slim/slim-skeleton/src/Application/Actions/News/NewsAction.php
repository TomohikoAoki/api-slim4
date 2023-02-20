<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use App\Domain\News\NewsRepository;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use App\Application\Actions\Action;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

abstract class NewsAction extends Action
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * @var void
     */
    protected $query;


    /**
     * @param ContainerInterface $container
     * @param NewsRepository $newsRepository
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, NewsRepository $newsRepository, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->newsRepository = $newsRepository;
        parent::__construct($logger);

    }

    /**
     * @param  string $name
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolvePageQuery()
    {
        $page = $this->query['page'];
        
        if (!preg_match("/^[0-9]+$/",$page)) {
            throw new HttpBadRequestException($this->request, "クエリパラメータが不正な値です");
        }

        return $page;
    }

    /**
     * @param string $auth
     * @return bool
     * @throws HttpUnauthorizedException
     */
    protected function checkAuth(string $auth): bool
    {
        if(!in_array($auth, $this->request->getAttribute('user_auth'))) {
            throw new HttpUnauthorizedException($this->request, '権限がありません');
        }

        return true;
    }
    
}
