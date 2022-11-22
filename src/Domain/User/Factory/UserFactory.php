<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Application\Common\Factory\FactoryInterface;
use App\Domain\User\Entity\Role;
use App\Domain\User\Entity\User;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class UserFactory implements FactoryInterface
{
    public static function create(array $input): User
    {
        $faker = Factory::create();

        /** @var string $email */
        $email = $input['email'] ?? $faker->email;

        /** @var string $username */
        $username = $input['username'] ?? $faker->userName;

        /** @var Role $role */
        $role = $input['role'];

        /** @var string $password */
        $password = $input['password'] ?? $faker->password;

        $user = new User();

        if (isset($input['email'])) {
            $user->setEmail($email);
        }

        if (isset($input['username'])) {
            $user->setUsername($username);
        }

        if (isset($input['role'])) {
            $user->setRole($role);
        }

        if (isset($input['password'])) {
            $user->setPlainPassword($password);
        }

        if (isset($input['uuid'])) {
            /** @var Uuid $uuid */
            $uuid = $input['uuid'];

            $user->setUuid($uuid);
        }

        if (isset($input['imgPath'])) {
            /** @var string $imgPath */
            $imgPath = $input['imgPath'];

            $user->setImgPath($imgPath);
        }

        return $user;
    }
}
