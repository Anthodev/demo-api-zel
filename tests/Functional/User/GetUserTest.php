<?php

declare(strict_types=1);

namespace Tests\Functional\User;

use App\Domain\User\Enum\RoleCodeEnum;
use Tests\DataFixtures\User\UserFixtures;

beforeEach(function () {
    $this->loadBaseFixturesOnly();
});

it('can get myself', function (): void {
    $this->loginUser();

    $response = $this->getObjectResponseWithNoError(
        url: '/user/me',
    );

    expect($response->uuid)->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response->email)->toBe(UserFixtures::USER_ADMIN_EMAIL)
        ->and($response->username)->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response->role->code)->toBe(RoleCodeEnum::ROLE_ADMIN->value)
        ->and($response)->not->toHaveProperty('id')
        ->and($response)->not->toHaveProperty('password')
        ->and($response)->not->toHaveProperty('created_at')
        ->and($response)->not->toHaveProperty('updated_at')
    ;
});

it('can get another user', function (): void {
    $this->loginUser();

    $response = $this->getObjectResponseWithNoError(
        url: '/user/' . UserFixtures::USER1_USER_UUID,
    );

    expect($response->uuid)->toBe(UserFixtures::USER1_USER_UUID)
        ->and($response->email)->toBe(UserFixtures::USER1_USER_EMAIL)
        ->and($response->username)->toBe(UserFixtures::USER1_USER_USERNAME)
        ->and($response->role->code)->toBe(RoleCodeEnum::ROLE_USER->value)
        ->and($response)->not->toHaveProperty('id')
        ->and($response)->not->toHaveProperty('password')
        ->and($response)->not->toHaveProperty('created_at')
        ->and($response)->not->toHaveProperty('updated_at')
    ;
});

it('cannot get an unknown user', function (): void {
    $this->loginUser();

    $response = $this->getObjectResponseWithError(
        url: '/user/unknown',
    );
});

it('cannot get another user without being logged in', function (): void {
    $response = $this->getObjectResponseWithError(
        url: '/user/' . UserFixtures::USER1_USER_UUID,
    );

    expect($response->code)->toBe(401);
});
