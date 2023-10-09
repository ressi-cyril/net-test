<?php

namespace App\Controller;

use App\Entity\Category;
use App\Model\CategoryDto;
use App\Repository\CategoryRepository;
use App\Services\CategoryService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Requirement\Requirement;

#[OA\Tag('api_category')]
class CategoryController extends AbstractFOSRestController
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
    )]
    #[Rest\Get('/api/categories/', name: 'api_get_categories')]
    #[Rest\View(serializerGroups: ['category'])]
    public function getCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
        content: new Model(type: Category::class)
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
        name: 'categoryDto',
        content: new OA\JsonContent(
            ref: new Model(type: CategoryDto::class)
        )
    )]
    #[Rest\Post('api/categories/', name: 'api_category_create')]
    #[ParamConverter('categoryDto', class: CategoryDto::class, converter: "fos_rest.request_body")]
    #[Rest\View(serializerGroups: ['category'])]
    public function createCategory(CategoryDto $categoryDto): Category
    {
        return $this->categoryService->create($categoryDto);
    }

    #[OA\Response(
        response: 200,
        description: 'Returned when successful',
        content: new Model(type: Category::class)
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
    #[Rest\Put('/api/categories/{category}', name: 'api_category_update', requirements: ['category' => Requirement::UUID_V7])]
    #[Rest\View(serializerGroups: ['category'])]
    #[ParamConverter('categoryDto', class: CategoryDto::class, converter: "fos_rest.request_body")]
    public function updateCategory(Category $category, CategoryDto $categoryDto): Category
    {
        $this->categoryService->update($categoryDto, $category);

        return $category;
    }
}