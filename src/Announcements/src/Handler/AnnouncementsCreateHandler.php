<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Announcements\Entity\Announcement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class AnnouncementsCreateHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;

    public function __construct(
        EntityManager $entityManager,
        HalResponseFactory $halResponseFactory,
        ResourceGenerator $resourceGenerator
    )
    {
        $this->entityManager = $entityManager;
        $this->halResponseFactory = $halResponseFactory;
        $this->resourceGenerator = $resourceGenerator;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $requestBody = $request->getParsedBody()['Request']['Announcements'];
        if (empty($requestBody)) {
            $result['_error']['error'] = 'missing request';
            $result['_error']['error description'] = 'No request body sent.';
            return new JsonResponse($result, 400);
        }
        $entity = new Announcement();
        try {
            $entity->setAnnouncement($requestBody);
            $entity->setCreated(new \DateTime('now'));
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            $result['_error']['error'] = 'missing request';
            $result['_error']['error description'] = $e->getMessage();
            return new JsonResponse($result, 500);
        }
        $resource = $this->resourceGenerator->fromObject($entity, $request);
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}


// An exception occurred while executing 'INSERT INTO announcements (sort, content, is_active, created, modified) 
// VALUES (?, ?, ?, ?, ?)' with params [10, "mis huevos", 1, "2020-11-20 02:42:31", "2020-11-20 02:42:31"]: 
// SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value


// Service with name "Doctrine\ORM\EntityManager" could not be created. Reason: An exception occurred in driver:
//  SQLSTATE[HY000] [2002] Connection refused

// Service with name "Doctrine\ORM\EntityManager" could not be created. 
// Reason: An exception occurred in driver: SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo
