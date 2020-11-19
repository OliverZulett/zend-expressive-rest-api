<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Announcements\Entity\AnnouncementCollection;
use Announcements\Entity\Announcement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Helper\UrlHelper;

class AnnouncementsReadHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $pageCount;
    protected $resourceGenerator;
    protected $halResponseFactory;

    public function __construct(EntityManager $entityManager, $pageCount, ResourceGenerator $resourceGenerator, HalResponseFactory $halResponseFactory)
    {
        $this->entityManager = $entityManager;
        $this->pageCount = $pageCount;
        $this->resourceGenerator = $resourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {

        $repository = $this->entityManager->getRepository(Announcement::class);
        $query = $repository
                    ->createQueryBuilder('a')
                    ->addOrderBy('a.sort', 'asc')
                    ->setMaxResults($this->pageCount)
                    ->getQuery();
        $paginator = new AnnouncementCollection($query);
        $resource = $this->resourceGenerator->fromObject($paginator, $request);
        return $this->halResponseFactory->createResponse($request, $resource);

        // $query = $this->entityManager->getRepository('Announcements\Entity\Announcement')->createQueryBuilder('c')->getQuery();
        // $paginator = new Paginator($query);
        // $totalItems = count($paginator);
        // $currentPage = ($request->getAttribute('page')) ?: 1;
        // $totalPageCount = ceil($totalItems/$this->pageCount);
        // $nextPage = (($currentPage < $totalPageCount) ? $currentPage + 1 :  $totalPageCount);
        // $previousPage = (($currentPage > 1) ? $currentPage -1 : 1);
        // $records = $paginator
        //     ->getQuery()
        //     ->setFirstResult($this->pageCount * ($currentPage-1))
        //     ->setMaxResults($this->pageCount)
        //     ->getResult(Query::HYDRATE_ARRAY);
        // $result['_per_page'] = $this->pageCount;
        // $result['_page'] = $currentPage;
        // $result['_total'] = $totalItems;
        // $result['_total_pages'] = $totalPageCount;
        // $result['_links']['self'] = $this->urlHelper->generate('announcements.read', ['page' => $currentPage]);
        // $result['_links']['first'] = $this->urlHelper->generate('announcements.read', ['page' => 1]);
        // $result['_links']['previous'] = $this->urlHelper->generate('announcements.read', ['page' => $previousPage]);
        // $result['_links']['next'] = $this->urlHelper->generate('announcements.read', ['page' => $nextPage]);
        // $result['_links']['last'] = $this->urlHelper->generate('announcements.read', ['page' => $totalPageCount]);
        // $result['_links']['create'] = $this->urlHelper->generate('announcements.create');
        // $result['_links']['read'] = $this->urlHelper->generate('announcements.read', ['page' => 1]);
        // foreach ($records as $key => $value) {
        //     $records[$key]['_links']['self'] = $this->urlHelper->generate('announcements.view', ['id' => $value['id']]);
        //     $records[$key]['_links']['update'] = $this->urlHelper->generate('announcements.update', ['id' => $value['id']]);
        //     $records[$key]['_links']['delete'] = $this->urlHelper->generate('announcements.delete', ['id' => $value['id']]);
        // $result['_embedded']['Announcement'] = $records;
        // }
        // return new JsonResponse($result);
    }
}
