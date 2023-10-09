<?php

namespace UnitTest;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CategoryTest extends TestCase
{
    public function testGettersAndSettersCategory()
    {
        // Initialize test data
        $childCategory = new Category();
        $category = new Category();
        $parentCategory = new Category();
        $uuid = Uuid::v4();
        $name = 'Test Category';
        $urlRewrite = 'test-category';
        $permalink = 'test-category-permalink';

        // Set properties
        $category
            ->setId($uuid)
            ->setName($name)
            ->setUrlRewrite($urlRewrite)
            ->setPermalink($permalink)
            ->setParent($parentCategory)
            ->addChildren($childCategory);

        $parentCategory
            ->setId(Uuid::v4())
            ->setName('Parent Category');

        $childCategory
            ->setId(Uuid::v4())
            ->setName('Child Category');


        // Assert that the getters return the expected values
        $this->assertEquals($uuid, $category->getId());
        $this->assertEquals($name, $category->getName());
        $this->assertEquals($urlRewrite, $category->getUrlRewrite());
        $this->assertEquals($permalink, $category->getPermalink());
        $this->assertEquals($parentCategory, $category->getParent());
        $this->assertCount(1, $category->getChildren());
        $this->assertTrue($category->getChildren()->contains($childCategory));

        // Test removing child category
        $category->removeChildren($childCategory);
        $this->assertCount(0, $category->getChildren());
    }
}
