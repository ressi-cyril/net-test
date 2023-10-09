<?php

namespace UnitTest;

use App\Entity\Category;
use App\Entity\Page;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PageTest extends TestCase
{
    public function testGettersAndSettersPage()
    {
        // Initialize test data
        $user = new User();
        $category = new Category();
        $page = new Page();
        $uuid = Uuid::v4();
        $idPage = 1;
        $title = 'Test Title';
        $resume = 'Test Resume';
        $content = 'Test Content';
        $dateUpdate = new \DateTime();
        $status = 1;
        $trackingView = 0;
        $urlRewrite = 'test-title';
        $permalink = 'test-permalink';

        $user
            ->setId(Uuid::v4())
            ->setEmail('user@example.com')
            ->setFullName('Test User');


        $category
            ->setId(Uuid::v4())
            ->setName('Test Category')
            ->setUrlRewrite('test-category')
            ->setPermalink('test-category');

        $page
            ->setId($uuid)
            ->setIdPage($idPage)
            ->setTitle($title)
            ->setResume($resume)
            ->setContent($content)
            ->setDateUpdate($dateUpdate)
            ->setStatus($status)
            ->setTrackingView($trackingView)
            ->setUser($user)
            ->setMainCategory($category)
            ->setUrlRewrite($urlRewrite)
            ->setPermalink($permalink);

        // Assert that the getters return the expected values
        $this->assertEquals($uuid, $page->getId());
        $this->assertEquals($idPage, $page->getIdPage());
        $this->assertEquals($title, $page->getTitle());
        $this->assertEquals($resume, $page->getResume());
        $this->assertEquals($content, $page->getContent());
        $this->assertEquals($dateUpdate, $page->getDateUpdate());
        $this->assertEquals($status, $page->getStatus());
        $this->assertEquals($trackingView, $page->getTrackingView());
        $this->assertEquals($user, $page->getUser());
        $this->assertEquals($category, $page->getMainCategory());
        $this->assertEquals($urlRewrite, $page->getUrlRewrite());
        $this->assertEquals($permalink, $page->getPermalink());
    }

    public function testGetOrderedCategories()
    {
        // Create Category entities for testing
        $rootCategory = new Category();
        $rootCategory->setName('Root');

        $childCategory = new Category();
        $childCategory
            ->setName('Child')
            ->setParent($rootCategory);

        $grandChildCategory = new Category();
        $grandChildCategory
            ->setName('GrandChild')
            ->setParent($childCategory);

        // Create a new Page entity and set its categories
        $page = new Page();
        $page
            ->addCategory($rootCategory)
            ->addCategory($childCategory)
            ->addCategory($grandChildCategory);

        // Call the getOrderedCategories method
        $result = $page->getOrderedCategories();

        // Assert that the result is as expected
        $this->assertEquals('Root > Child > GrandChild', $result);
    }
}
