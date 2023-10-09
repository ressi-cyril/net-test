<?php

namespace EndToEnd;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private static KernelBrowser $client;
    private static ObjectManager $entityManager;
    private static array $categories;

    public static function setUpBeforeClass(): void
    {
        // Initialize client and services
        self::$client = static::createClient();
        self::$entityManager = static::getContainer()->get('doctrine')->getManager();
        self::$categories = self::$entityManager->getRepository(Category::class)->findAll();
    }

    public function testGetCategoriesOk200()
    {
        // Request api_get_pages
        self::$client->request('GET', '/api/categories/');

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
    }

    public function testCreateCategoryOk200()
    {
        // Creates resources
        $categoryDto = [
            'name' => 'New Category',
        ];

        // Request api_category_create
        self::$client->request(
            'POST',
            '/api/categories/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($categoryDto)
        );

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        // Get the response data as an associative array
        $responseData = json_decode(self::$client->getResponse()->getContent(), true);

        // Assert data matches the created Page, serialization group "category"
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('New Category', $responseData['name']);
        $this->assertEquals('new-category', $responseData['url_rewrite']);
        $this->assertEquals('new-category', $responseData['permalink']);
        $this->assertArrayHasKey('pages', $responseData);
    }

    public function testCreateCategoryFail400()
    {
        // Creates invalid resources
        $categoryDto = [];

        // Request api_page_create
        self::$client->request(
            'POST',
            '/api/categories/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($categoryDto)
        );


        // Assert response code
        $this->assertEquals(400, self::$client->getResponse()->getStatusCode());
    }

    public function testUpdateCategoryOk200()
    {
        // Creates resources
        $categoryDto = [
            'name' => 'Update Category',
            'id_parent' => self::$categories[9]->getId()
        ];

        // Request api_category_create
        self::$client->request(
            'PUT',
            '/api/categories/' . self::$categories[1]->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($categoryDto)
        );

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        // Get the response data as an associative array
        $responseData = json_decode(self::$client->getResponse()->getContent(), true);


        // Assert data matches the created Page, serialization group "category"
        $this->assertEquals(self::$categories[1]->getId(), $responseData['id']);
        $this->assertEquals('Update Category', $responseData['name']);
        $this->assertEquals('update-category', $responseData['url_rewrite']);
        $this->assertEquals('parent1/category10/sub101/update-category', $responseData['permalink']);
        $this->assertArrayHasKey('pages', $responseData);
    }

    public function testUpdateCategoryFail400()
    {
        // Creates invalid resources
        $categoryDto = [];

        // Request api_category_create
        self::$client->request(
            'PUT',
            '/api/categories/' . self::$categories[1]->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($categoryDto)
        );

        // Assert response code
        $this->assertEquals(400, self::$client->getResponse()->getStatusCode());
    }

}
