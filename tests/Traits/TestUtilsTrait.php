<?php

declare(strict_types=1);

namespace Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

trait TestUtilsTrait
{
    public function getSecurity(): Security
    {
        return static::$client->getContainer()->get(Security::class);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return static::$client->getContainer()->get(EntityManagerInterface::class);
    }
}
