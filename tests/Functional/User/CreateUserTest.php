<?php

declare(strict_types=1);

namespace Tests\Functional\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

it('can create an user', function (): void {
    $response = $this->getObjectResponseWithNoError(
        data: '
            {
                "email": "test@test.io",
                "username": "test",
                "password": "Test@1234",
                "passwordConfirmation": "Test@1234"
            }
        ',
        method: Request::METHOD_POST,
        url: '/register',
    );

    expect($response->username)->toBe('test')
        ->and($response->role->code)->toBe(RoleCodeEnum::ROLE_ADMIN->value)
    ;

    /** @var EntityManagerInterface $entityManager */
    $entityManager = $this->getEntityManager();

    /** @var ?User $user */
    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'test']);

    expect($user)
        ->not()->toBeNull()
        ->and($user->getEmail())->toBe('test@test.io')
        ->and($user->getUsername())->toBe('test')
        ->and($user->getRole()->getCode())->toBe(RoleCodeEnum::ROLE_ADMIN->value)
    ;
});

it('cannot create an existing user', function (): void {
    $this->loadBaseFixturesOnly();

    $response = $this->getObjectResponseWithError(
        data: '
            {
                "email": "admin@test.io",
                "username": "admin",
                "password": "Test@1234",
                "passwordConfirmation": "Test@1234"
            }
        ',
        method: Request::METHOD_POST,
        url: '/register',
    );

    expect($response->status)->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->detail)->toBe('User already exists')
    ;
});

it('cannot create a user without an email', function (): void {
    $response = $this->getObjectResponseWithError(
        data: '
            {
                "username": "test",
                "password": "Test@1234",
                "passwordConfirmation": "Test@1234"
            }
        ',
        method: Request::METHOD_POST,
        url: '/register',
    );

    expect($response->status)->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->detail)->toBe('The email, username and password cannot be null.')
    ;
});

it('cannot create a user without an username', function (): void {
    $response = $this->getObjectResponseWithError(
        data: '
            {
                "email": "test@test.io",
                "password": "Test@1234",
                "passwordConfirmation": "Test@1234"
            }
        ',
        method: Request::METHOD_POST,
        url: '/register',
    );

    expect($response->status)->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->detail)->toBe('The email, username and password cannot be null.')
    ;
});

it('cannot create a user without a password', function (): void {
    $response = $this->getObjectResponseWithError(
        data: '
            {
                "email": "test@test.io",
                "username": "test"
            }
        ',
        method: Request::METHOD_POST,
        url: '/register',
    );

    expect($response->status)->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->detail)->toBe('The email, username and password cannot be null.')
    ;
});

it('cannot create a user with a non matching password confirmation', function (): void {
    $response = $this->getObjectResponseWithError(
        data: '
            {
                "email": "test@test.io",
                "username": "test",
                "password": "Test@1234",
                "passwordConfirmation": "test"
            }
        ',
        method: Request::METHOD_POST,
        url: '/register',
    );

    expect($response->status)->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->detail)->toBe('Password confirmation does not match')
    ;
});
