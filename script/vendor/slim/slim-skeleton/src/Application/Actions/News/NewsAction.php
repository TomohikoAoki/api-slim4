<?php
declare(strict_types=1);

namespace App\Application\Actions\News;

use App\Domain\News\NewsRepository;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class NewsAction
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
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @param NewsRepository $newsRepository
     * @param Twig $twig
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, NewsRepository $newsRepository, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->newsRepository = $newsRepository;
        $this->logger = $logger;

    }
    
}
