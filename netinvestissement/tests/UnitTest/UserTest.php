<?php

namespace UnitTest;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UserTest extends TestCase
{
    public function testGettersAndSettersUser()
    {
        // Initialize test data
        $parentUser = new User();
        $user = new User();
        $uuid = Uuid::v4();
        $email = 'test@example.com';
        $fullName = 'Test User';

        // Set properties
        $user
            ->setId($uuid)
            ->setEmail($email)
            ->setFullName($fullName);

        $parentUser
            ->setId(Uuid::v4())
            ->setEmail('parent@example.com')
            ->setFullName('Parent User');

        // Assert that the getters return the expected values
        $this->assertEquals($uuid, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($fullName, $user->getFullName());
        $user->setParent($parentUser);
        $this->assertEquals($parentUser, $user->getParent());
    }
}
