<?php

namespace App\Services;

use App\Entity\Category;
use App\Interfaces\ServiceInterface;
use App\Model\CategoryDto;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoryService implements ServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository $repository,
        private readonly ValidationService $validationService,
        private readonly PageService $pageService
    ) {
    }

    /**
     * Create a new Category entity.
     * @param CategoryDto $categoryDto
     * @return Category
     */
    public function create(CategoryDto $categoryDto): Category
    {
        $category = new Category();

        // Verify required fields
        $this->validationService->performEntityValidation($categoryDto, ['create']);

        // Populate the category with necessary fields
        $this->populate($categoryDto, $category);

        // Validate and save the category with 'create' validation group
        $this->validateAndSave($category, ['create']);

        return $category;
    }

    /**
     * Update an existing Category entity.
     * @param CategoryDto $categoryDto
     * @param Category $category
     */
    public function update(CategoryDto $categoryDto, Category $category): void
    {
        // Verify required fields
        $this->validationService->performEntityValidation($categoryDto, ['update']);

        // Populate the category with necessary fields
        $this->populate($categoryDto, $category, false);

        // Validate and save the category with 'update' validation group
        $this->validateAndSave($category, ['update']);

        // Update Pages since parents properties values has changed
        foreach ($category->getPages() as $page) {
            $this->pageService->updateFromCategoryChange($page);
        }
    }

    /**
     * Populate the Category entity with specific treatments.
     * @param CategoryDto $categoryDto
     * @param Category $category
     * @param bool $isCreating
     */
    public function populate(CategoryDto $categoryDto, Category $category, bool $isCreating = true): void
    {
        // Set the name of the category
        $category->setName($categoryDto->name);

        // Sanitize the name and set the 'url_rewrite' field
        $urlRewrite = $this->sanitizeName($category->getName());
        $category->setUrlRewrite($urlRewrite);

        // If updating
        if ($isCreating !== true) {
            // Update the main (parent) category
            $this->UpdateMainCategory($category, $categoryDto->idParent);
        } else {
            // Generate and set the new permalink for the current category
            $permalink = $this->generatePermalink($category);
            $category->setPermalink($permalink);
        }
    }

    /**
     * Update the main (parent) category of the given category
     * @param Category $categoryToUpdate
     * @param string $uuid
     */
    private function UpdateMainCategory(Category $categoryToUpdate, string $uuid): void
    {
        // A category cannot have itself as a parent and the parent must be different
        if ($categoryToUpdate->getParent() !== $categoryToUpdate && $categoryToUpdate->getParent() !== $uuid) {
            $newMainCategory = $this->repository->findOneByOrThrow(['id' => $uuid]);

            // Two categories cannot be the parent of each other.
            if ($newMainCategory->getParent() !== $categoryToUpdate) {
                $categoryToUpdate->setParent($newMainCategory);

                // Update the permalink recursively for this category and its descendants
                $this->updatePermalinkRecursively($categoryToUpdate);
            } else {
                throw new BadRequestHttpException("The new main category cannot have the category being updated as its parent.");
            }
        }
    }

    /**
     * Sanitize name by replacing special characters and accents
     * @param string $name
     * @return string
     */
    private function sanitizeName(string $name): string
    {
        // Convert the string to lowercase
        $name = mb_strtolower($name);

        // Remove accents and special characters
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        // Replace any non-alphanumeric characters with hyphens
        $name = preg_replace('/[^a-z0-9]/', '-', $name);

        return $name;
    }

    /**
     * Update the permalink of the given category and its descendants recursively.
     * @param Category $category
     */
    private function updatePermalinkRecursively(Category $category): void
    {
        // Generate and set the new permalink for the current category
        $permalink = $this->generatePermalink($category);
        $category->setPermalink($permalink);

        //Validate and save the updated category
        $this->validateAndSave($category, ['update']);

        // Recursively update the permalink for all child categories
        foreach ($category->getChildren() as $child) {
            $this->updatePermalinkRecursively($child);
        }
    }

    /**
     * Generate the permalink for a given category by concatenating the 'url_rewrite' fields
     * of the category and all its parents.
     * @param Category $category
     * @return string
     */
    private function generatePermalink(Category $category): string
    {
        if ($category->getParent() !== $category) {
            // Initialize an array to hold the parts of the permalink
            $permalinkParts = [];

            // Start with the current category
            $currentCategory = $category;

            // Traverse up the category tree to the root
            while ($currentCategory !== null) {
                // If the 'url_rewrite' field is set, prepend it to the permalink parts
                if ($currentCategory->getUrlRewrite() !== null) {
                    array_unshift($permalinkParts, $currentCategory->getUrlRewrite());
                }

                // Move to the parent category for the next iteration
                if ($currentCategory->getParent() !== null) {
                    $currentCategory = $currentCategory->getParent();
                } else {
                    // Break the loop if there is no parent
                    $currentCategory = null;
                }
            }

            // Concatenate the permalink parts with slashes to form the final permalink
            return implode('/', $permalinkParts);
        } else {
            return $category->getPermalink();
        }
    }

    /**
     * Validate and save to DB given object
     * @param object $object
     * @param array|null $groups
     * @return void
     */
    public function validateAndSave(object $object, ?array $groups = null): void
    {
        // Verify entity validation
        $this->validationService->performEntityValidation($object, $groups);

        // Save entity to Database
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }
}