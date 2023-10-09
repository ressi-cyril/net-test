<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Page;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Services\CategoryService;
use App\Services\PageService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly UserRepository $userRepository,
        private readonly PageService $pageService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Creates 2 Parent Categories
        for ($i = 0; $i <= 1; $i++) {
            $parentCategory = new Category();
            $parentCategory
                ->setName('parent' . $i)
                ->setUrlRewrite('parent' . $i)
                ->setPermalink('parent' . $i);


            $manager->persist($parentCategory);

            // Creates  3 Category for each Parent
            for ($j = 0; $j <= 2; $j++) {
                $category = new Category();
                $category
                    ->setName('category' . $i . $j)
                    ->setUrlRewrite('category' . $i . $j)
                    ->setPermalink($parentCategory->getUrlRewrite() . '/' . $category->getUrlRewrite())
                    ->setParent($parentCategory);

                $manager->persist($category);

                // Creates 1 Sub category for each Category
                $subCategory = new Category();
                $subCategory
                    ->setName('sub' . $i . $j . '1')
                    ->setUrlRewrite('sub' . $i . $j . '1')
                    ->setPermalink($parentCategory->getUrlRewrite() . '/' . $category->getUrlRewrite() . '/' . $subCategory->getUrlRewrite())
                    ->setParent($category);

                $manager->persist($subCategory);
            }
        }
        $manager->flush();

        // Creates 5 Parent Users
        for ($i = 0; $i <= 4; $i++) {
            $parent = new User();
            $parent
                ->setEmail($faker->email())
                ->setFullName($faker->name());

            $manager->persist($parent);

            // 1 User for each Parent
            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setFullName($faker->name())
                ->setParent($parent);

            $manager->persist($user);
        }
        $manager->flush();

        // Creates 5 Pages
        $categories = $this->categoryRepository->findAll();
        $users = $this->userRepository->findAll();

        for ($i = 0; $i <= 4; $i++) {
            $page = new Page();
            $page
                ->setTitle('page' . $i)
                ->setResume($faker->words(10, true))
                ->setContent($faker->text(220))
                ->setDateUpdate(new \DateTime())
                ->setUser($users[$i])
                ->setMainCategory($categories[$i])
                ->setUrlRewrite('page' . $i)
                ->setPermalink($categories[$i]->getPermalink() . '/' . $page->getUrlRewrite());

            $this->pageService->assignParentCategories($page);

            $manager->persist($page);
        }

        $manager->flush();
    }
}
