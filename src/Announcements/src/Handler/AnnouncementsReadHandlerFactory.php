<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class AnnouncementsReadHandlerFactory
{
    public function __invoke(ContainerInterface $container): AnnouncementsReadHandler
    {
        return new AnnouncementsReadHandler(
            $container->get(EntityManager::class),
            $container->get('config')['page_size'], 
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class)
        );
    }
}
