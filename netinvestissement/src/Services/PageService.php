<?php

namespace App\Services;

use App\Entity\Page;
use App\Interfaces\ServiceInterface;
use App\Model\PageDto;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class PageService implements ServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidationService $validationService,
        private readonly UserRepository $userRepository,
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Create a new Page entity
     * @param PageDto $pageDto
     * @return Page
     */
    public function create(PageDto $pageDto): Page
    {
        $page = new Page();

        // Verify required fields
        $this->validationService->performEntityValidation($pageDto, ['create']);

        // Populate the category with necessary fields
        $this->populate($page, $pageDto);

        // Validate and save the category with 'create' validation group
        $this->validateAndSave($page, ['create']);

        return $page;
    }

    /**
     * Update an existing Page entity
     * @param Page $page
     * @param PageDto $pageDto
     * @return Page
     */
    public function update(Page $page, PageDto $pageDto): Page
    {
        // Verify required fields
        $this->validationService->performEntityValidation($pageDto, ['update']);

        // Populate the category with necessary fields
        $this->populate($page, $pageDto, true);

        // Validate and save the page with 'update' validation group
        $this->validateAndSave($page, ['update']);

        return $page;
    }

    /**
     * Update the fullname of the user associated with a given page.
     * @param Page $page
     * @param PageDto $pageDto
     * @return void
     * Note: If there are more user-related functionalities needed in the future,
     * it would be beneficial to abstract those into a dedicated UserService.
     */
    public function updatePageUserFullName(Page $page, PageDto $pageDto): void
    {
        // Verify required fields
        $this->validationService->performEntityValidation($pageDto, ['page_fullname']);

        $page->getUser()->setFullName($pageDto->userNewFullname);

        // Validate and save the page with 'update' validation group
        $this->validateAndSave($page->getUser(), ['update']);
    }

    /**
     * Delete a Page entity
     * @param Page $page
     * @return void
     */
    public function delete(Page $page): void
    {
        $this->entityManager->remove($page);
        $this->entityManager->flush();
    }

    /**
     * Update Page entity when Category has been updated
     *
     * @param Page $page
     * @return void
     */
    public function updateFromCategoryChange(Page $page): void
    {
        $permalink = $this->generatePermalink($page);
        $page->setPermalink($permalink);

        $this->assignParentCategories($page);
    }

    /**
     * Populate a Page entity
     * @param Page $page
     * @param PageDto|null $pageDto
     * @param bool|null $updating
     */
    private function populate(Page $page, ?PageDto $pageDto = null, ?bool $updating = false): void
    {
        $this->initializePageUser($pageDto->userEmail, $page);
        $this->initializePageCategory($pageDto->mainCategory, $page);

        $resume = $this->truncateContent($pageDto->content);
        $urlRewrite = $this->sanitizeString($pageDto->title);

        $page
            ->setTitle($pageDto->title)
            ->setContent($pageDto->content)
            ->setUrlRewrite($urlRewrite)
            ->setDateUpdate(new \DateTime())
            ->setResume($resume);

        if ($updating) {
            $page
                ->setStatus($pageDto->status)
                ->setTrackingView($pageDto->trackingView);
        }


        $permalink = $this->generatePermalink($page);
        $page->setPermalink($permalink);

        $this->assignParentCategories($page);
    }

    /**
     * Find and Initialize User
     * @param string $userMail
     * @param Page $page
     * @return void
     */
    private function initializePageUser(string $userMail, Page $page): void
    {
        $user = $this->userRepository->findOneByOrThrow(['email' => $userMail]);
        $page->setUser($user);
    }

    /**
     * Find and Initialize Category
     * @param string $categoryName
     * @param Page $page
     * @return void
     */
    private function initializePageCategory(string $categoryName, Page $page): void
    {
        $category = $this->categoryRepository->findOneByOrThrow(['name' => $categoryName]);
        $page->setMainCategory($category);
    }

    /**
     * Truncate the content to 255 characters, ensuring the last word is complete, followed by "..."
     * @param string $content
     * @return string
     */
    private function truncateContent(string $content): string
    {
        if (strlen($content) <= 255) {
            return $content;
        }

        $truncated = substr($content, 0, 252);  // Cut off at 252 characters
        $lastSpace = strrpos($truncated, ' ');  // Find the last space position
        $truncated = substr($truncated, 0, $lastSpace);  // Cut off at the last whole word

        return $truncated . '...';
    }

    /**
     * Sanitize name by replacing special characters and accents.
     * @param string $string
     * @return string
     */
    private function sanitizeString(string $string): string
    {
        // Convert the string to lowercase
        $string = mb_strtolower($string);

        // Remove accents and special characters
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // Replace any non-alphanumeric characters with hyphens
        $string = preg_replace('/[^a-z0-9]/', '-', $string);

        return $string;
    }

    /**
     * Generate permalink by concatenating the permalink of main category and the article's rewrite
     * @param Page $page
     * @return string
     */
    public function generatePermalink(Page $page): string
    {
        $categoryPermalink = $page->getMainCategory()->getPermalink();
        $articleUrlRewrite = $page->getUrlRewrite();

        return $categoryPermalink . '/' . $articleUrlRewrite;
    }

    /**
     * Assign all parent categories of the main category to the given Page object
     * @param Page $page
     */
    public function assignParentCategories(Page $page): void
    {
        // Clear existing categories
        $page->getCategories()->clear();

        // Get the main category
        $mainCategory = $page->getMainCategory();

        // Initialize the current category to the main category
        $currentCategory = $mainCategory;

        // Loop through all parents and add them to the page's categories
        while ($currentCategory !== null) {
            $page->addCategory($currentCategory);

            // Move up to the parent category
            $currentCategory = $currentCategory->getParent();
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