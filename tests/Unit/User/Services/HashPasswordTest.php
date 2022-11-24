<?php

declare(strict_types=1);

namespace Tests\Unit\User\Services;

use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Service\HashPassword;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HashPasswordTest extends KernelTestCase
{
    private readonly HashPassword $hashPassword;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->hashPassword = $container->get(HashPassword::class);

        parent::setUp();
    }

    public function testHashPassword(): void
    {
        $user = UserFactory::create();

        $hashed = $this->hashPassword->hash($user, 'password');

        self::assertNotNull($hashed);
        self::assertNotSame('password', $hashed);
    }
}
