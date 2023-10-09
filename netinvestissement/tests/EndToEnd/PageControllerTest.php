<?php

namespace EndToEnd;

use App\Entity\Category;
use App\Entity\Page;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    private static KernelBrowser $client;
    private static ObjectManager $entityManager;
    private static array $categories;
    private static array $users;
    private static array $pages;

    public static function setUpBeforeClass(): void
    {
        // Initialize client and services
        self::$client = static::createClient();
        self::$entityManager = static::getContainer()->get('doctrine')->getManager();
        self::$categories = self::$entityManager->getRepository(Category::class)->findAll();
        self::$users = self::$entityManager->getRepository(User::class)->findAll();
        self::$pages = self::$entityManager->getRepository(Page::class)->findAll();
    }

    public function testGetPagesOk200()
    {
        // Request api_get_pages
        self::$client->request('GET', '/api/pages/');

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        // Get the response data as an associative array
        $responseData = json_decode(self::$client->getResponse()->getContent(), true);

        // Assert that each tournament in the response has the follow keys
        // Serialization group "pages"
        foreach ($responseData as $data) {
            $this->assertArrayHasKey('id', $data);
            $this->assertArrayHasKey('id_page', $data);
            $this->assertArrayHasKey('title', $data);
            $this->assertArrayHasKey('resume', $data);
            $this->assertArrayHasKey('date_update', $data);
            $this->assertArrayHasKey('status', $data);
            $this->assertArrayHasKey('tracking_view', $data);
            $this->assertArrayHasKey('user', $data);
            $this->assertArrayHasKey('id', $data['user']);
            $this->assertArrayHasKey('email', $data['user']);
            $this->assertArrayHasKey('full_name', $data['user']);
            $this->assertArrayHasKey('main_category', $data);
            $this->assertArrayHasKey('name', $data['main_category']);
            $this->assertArrayHasKey('url_rewrite', $data);
            $this->assertArrayHasKey('permalink', $data);
            $this->assertArrayHasKey('full_url', $data);
            $this->assertArrayHasKey('ordered_categories', $data);
        }
    }

    public function testGetPageOk200()
    {
        // Request api_get_pages
        self::$client->request('GET', '/api/pages/' . self::$pages[0]->getId());

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        // Get the response data as an associative array
        $responseData = json_decode(self::$client->getResponse()->getContent(), true);

        // Assert that each tournament in the response has the follow keys
        // Serialization group "page_detail"
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('id_page', $responseData);
        $this->assertArrayHasKey('title', $responseData);
        $this->assertArrayHasKey('resume', $responseData);
        $this->assertArrayHasKey('content', $responseData);
        $this->assertArrayHasKey('date_update', $responseData);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('tracking_view', $responseData);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('id', $responseData['user']);
        $this->assertArrayHasKey('email', $responseData['user']);
        $this->assertArrayHasKey('full_name', $responseData['user']);
        $this->assertArrayHasKey('parent', $responseData['user']);
        $this->assertArrayHasKey('main_category', $responseData);
        $this->assertArrayHasKey('name', $responseData['main_category']);
        $this->assertArrayHasKey('url_rewrite', $responseData);
        $this->assertArrayHasKey('permalink', $responseData);
        $this->assertArrayHasKey('ordered_categories', $responseData);
        $this->assertArrayHasKey('categories', $responseData);
        $this->assertArrayHasKey('name', $responseData['categories'][0]);
        $this->assertArrayHasKey('full_url', $responseData);
    }

    public function testCreatePageOk200()
    {
        // Creates resources
        $pageDto = [
            'title' => 'une Page de test200',
            'content' => 'un content court',
            'user_email' => self::$users[0]->getEmail(),
            'main_category' => self::$categories[0]->getName(),
        ];

        // Request api_page_create
        self::$client->request(
            'POST',
            '/api/pages/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($pageDto)
        );

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        // Get the response data as an associative array
        $responseData = json_decode(self::$client->getResponse()->getContent(), true);

        // Assert data matches the created Page, serialization group "page_detail"
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('id_page', $responseData);
        $this->assertEquals('une Page de test200', $responseData['title']);
        $this->assertArrayHasKey('resume', $responseData);
        $this->assertEquals('un content court', $responseData['content']);
        $this->assertArrayHasKey('date_update', $responseData);
        $this->assertEquals(1, $responseData['status']);
        $this->assertEquals(0, $responseData['tracking_view']);
        $this->assertArrayHasKey('id', $responseData['user']);
        $this->assertEquals(self::$users[0]->getId(), $responseData['user']['id']);
        $this->assertEquals(self::$users[0]->getEmail(), $responseData['user']['email']);
        $this->assertEquals(self::$users[0]->getFullName(), $responseData['user']['full_name']);
        $this->assertEquals(self::$categories[0]->getName(), $responseData['main_category']['name']);
        $this->assertEquals('une-page-de-test200', $responseData['url_rewrite']);
        $this->assertEquals('parent0/une-page-de-test200', $responseData['permalink']);
        $this->assertArrayHasKey('ordered_categories', $responseData);
        $this->assertArrayHasKey('categories', $responseData);
        $this->assertEquals('parent0/une-page-de-test200-0.html', $responseData['full_url']);
    }

    public function testCreatePageFail400()
    {
        // Creates invalid resources
        $pageDto = [];

        // Request api_page_create with Invalid Dto
        self::$client->request(
            'POST',
            '/api/pages/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($pageDto)
        );

        // Assert response code
        $this->assertEquals(400, self::$client->getResponse()->getStatusCode());
    }

    public function testUpdatePageOk200()
    {
        // Creates resources
        $pageDto = [
            'title' => 'un nouveau titre de page',
            'content' => 'un content pas court',
            'user_email' => self::$users[0]->getEmail(),
            'main_category' => self::$categories[0]->getName(),
            'status' => 0,
            'tracking_view' => 1
        ];

        // Request api_page_update
        self::$client->request(
            'PUT',
            '/api/pages/' . self::$pages[0]->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($pageDto)
        );

        // Assert response code
        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());

        // Get the response data as an associative array
        $responseData = json_decode(self::$client->getResponse()->getContent(), true);

        // Assert data matches the updated Page, serialization group "page_detail"
        $this->assertEquals(self::$pages[0]->getId(), $responseData['id']);
        $this->assertArrayHasKey('id_page', $responseData);
        $this->assertEquals('un nouveau titre de page', $responseData['title']);
        $this->assertArrayHasKey('resume', $responseData);
        $this->assertEquals('un content pas court', $responseData['content']);
        $this->assertArrayHasKey('date_update', $responseData);
        $this->assertEquals(0, $responseData['status']);
        $this->assertEquals(1, $responseData['tracking_view']);
        $this->assertArrayHasKey('id', $responseData['user']);
        $this->assertEquals(self::$users[0]->getId(), $responseData['user']['id']);
        $this->assertEquals(self::$users[0]->getEmail(), $responseData['user']['email']);
        $this->assertEquals(self::$users[0]->getFullName(), $responseData['user']['full_name']);
        $this->assertEquals(self::$categories[0]->getName(), $responseData['main_category']['name']);
        $this->assertEquals('un-nouveau-titre-de-page', $responseData['url_rewrite']);
        $this->assertEquals('parent0/un-nouveau-titre-de-page', $responseData['permalink']);
        $this->assertArrayHasKey('ordered_categories', $responseData);
        $this->assertArrayHasKey('categories', $responseData);
        $this->assertEquals('parent0/un-nouveau-titre-de-page-1.html', $responseData['full_url']);
    }

    public function testUpdatePageFail400()
    {
        // Creates invalid resources
        $pageDto = [];

        // Request api_page_update with Invalid Dto
        self::$client->request(
            'PUT',
            '/api/pages/' . self::$pages[0]->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($pageDto)
        );

        // Assert response code
        $this->assertEquals(400, self::$client->getResponse()->getStatusCode());
    }

    public function testDeleteOk204()
    {
        // Request api_page_delete
        self::$client->request('DELETE', '/api/pages/' . self::$pages[2]->getId());

        // Assert response code
        $this->assertEquals(204, self::$client->getResponse()->getStatusCode());
    }

    public function testUpdateUserFullNameOk200()
    {
        // Create resources
        $pageDto = ['user_new_fullname' => 'User new FullName'];

        // Request api_page_user_fullname
        self::$client->request(
            'PATCH',
            '/api/pages/' . self::$pages[0]->getId() . '/user-fullname',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($pageDto)
        );

        // Assert response code
        $this->assertEquals(204, self::$client->getResponse()->getStatusCode());
    }

    public function testUpdateUserFullNameFail400()
    {
        // Create invalid resources
        $pageDto = [];

        // Request api_page_user_fullname
        self::$client->request(
            'PATCH',
            '/api/pages/' . self::$pages[0]->getId() . '/user-fullname',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($pageDto)
        );

        // Assert response code
        $this->assertEquals(400, self::$client->getResponse()->getStatusCode());
    }
}
