<?php

namespace App\Controller;

use App\Entity\Page;
use App\Model\CategoryDto;
use App\Model\PageDto;
use App\Repository\PageRepository;
use App\Services\PageService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[OA\Tag('api_page')]
class PageController extends AbstractFOSRestController
{
    public function __construct(private readonly PageService $pageService)
    {
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Page::class))
        )
    )]
    #[Rest\Get('/api/pages/', name: 'api_pages_get')]
    #[Rest\View(serializerGroups: ['pages'])]
    public function getPages(PageRepository $pageRepository): array
    {
        return $pageRepository->findAll();
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
        content: new Model(type: Page::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Returned when entity not found',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotFoundHttpException::class))
        )
    )]
    #[Rest\Get('/api/pages/{page}', name: 'api_page_get', requirements: ['page' => Requirement::UUID_V7])]
    #[Rest\View(serializerGroups: ['page_detail'])]
    public function getPage(Page $page): Page
    {
        return $page;
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
        content: new Model(type: Page::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when entity has errors',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: HttpException::class))
        )
    )]
    #[OA\Parameter(
        name: 'pageDto',
        content: new OA\JsonContent(
            ref: new Model(type: PageDto::class)
        )
    )]
    #[Rest\Post('/api/pages/', name: 'api_page_create')]
    #[ParamConverter('pageDto', class: PageDto::class, converter: "fos_rest.request_body")]
    #[Rest\View(serializerGroups: ['page_detail'])]
    public function createPage(PageDto $pageDto): Page
    {
        return $this->pageService->create($pageDto);
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
        content: new Model(type: Page::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when entity has errors',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: HttpException::class))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Returned when entity not found',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotFoundHttpException::class))
        )
    )]
    #[OA\Parameter(
        name: 'categoryDto',
        content: new OA\JsonContent(
            ref: new Model(type: CategoryDto::class)
        )
    )]
    #[Rest\Put('/api/pages/{page}', name: 'api_page_update', requirements: ['page' => Requirement::UUID_V7])]
    #[Rest\View(serializerGroups: ['page_detail'])]
    #[ParamConverter('pageDto', class: PageDto::class, converter: "fos_rest.request_body")]
    public function updatePage(Page $page, PageDto $pageDto): Page
    {
        return $this->pageService->update($page, $pageDto);
    }

    #[OA\Response(
        response: 204,
        description: 'Returned when successful',
    )]
    #[OA\Response(
        response: 404,
        description: 'Returned when entity not found',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotFoundHttpException::class))
        )
    )]
    #[Route('/api/pages/{page}', name: 'api_page_delete', methods: ['DELETE'])]
    public function delete(Page $page): void
    {
        $this->pageService->delete($page);
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when entity has errors',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: HttpException::class))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Returned when entity not found',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotFoundHttpException::class))
        )
    )]
    #[Route('/api/pages/{page}/user-fullname', name: 'api_page_user_fullname', requirements: ['page' => Requirement::UUID_V7], methods: ['PATCH'])]
    #[Rest\View(serializerGroups: ['page_detail'])]
    #[ParamConverter('pageDto', class: PageDto::class, converter: "fos_rest.request_body")]
    public function updateUserFullName(Page $page, PageDto $pageDto): void
    {
        $this->pageService->updatePageUserFullName($page, $pageDto);
    }
}