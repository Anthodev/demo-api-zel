<?php

declare(strict_types=1);

namespace Tests\Functional\Article;

use App\Domain\Article\Enum\ArticleStatusEnum;
use Tests\DataFixtures\Article\UpdateArticle\ArticleFixtures;
use Tests\DataFixtures\User\UserFixtures;

beforeEach(function (): void {
    $this->loadAdditionalFixtures(
        [
            $this->dataFixtureDir . '/Article/UpdateArticle/Article.yaml',
        ]
    );

    $this->loginUser();
});

it('can get an article', function (): void {
    $response = $this->getObjectResponseWithNoError(
        url: sprintf('/article/%s', ArticleFixtures::ARTICLE_DRAFT_UUID),
    );

    expect($response->title)->toBe('Article Draft Fixture')
        ->and($response->content)->toBeString()
        ->and($response->status)->toBe(ArticleStatusEnum::DRAFT->value)
        ->and($response->publishedAt)->not->toBeNull()
        ->and($response->author->uuid)->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response->author->username)->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response->author->email)->toBe(UserFixtures::USER_ADMIN_EMAIL)
    ;
});

it('can get a list of articles', function (): void {
    $response = $this->getArrayResponseWithNoError(
        url: '/articles',
    );

    expect(count($response))->not->toBe(3)
        ->and(count($response))->toBe(2)
        ->and($response[0]['title'])->toBe('Article Published Fixture')
        ->and($response[0]['content'])->toBeString()
        ->and($response[0]['status'])->toBe(ArticleStatusEnum::PUBLISHED->value)
        ->and($response[0]['publishedAt'])->not->toBeNull()
        ->and($response[0]['author']['uuid'])->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response[0]['author']['username'])->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response[0]['author']['email'])->toBe(UserFixtures::USER_ADMIN_EMAIL)
        ->and($response[1]['title'])->toBe('Article Draft Fixture')
        ->and($response[1]['content'])->toBeString()
        ->and($response[1]['status'])->toBe(ArticleStatusEnum::DRAFT->value)
        ->and($response[1]['publishedAt'])->not->toBeNull()
        ->and($response[1]['author']['uuid'])->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response[1]['author']['username'])->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response[1]['author']['email'])->toBe(UserFixtures::USER_ADMIN_EMAIL)
    ;
});

it('can get list of published articles', function (): void {
    $response = $this->getArrayResponseWithNoError(
        url: '/articles/published',
    );

    expect(count($response))->toBe(1)
        ->and($response[0]['title'])->toBe('Article Published Fixture')
        ->and($response[0]['content'])->toBeString()
        ->and($response[0]['status'])->toBe(ArticleStatusEnum::PUBLISHED->value)
        ->and($response[0]['publishedAt'])->not->toBeNull()
        ->and($response[0]['author']['uuid'])->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response[0]['author']['username'])->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response[0]['author']['email'])->toBe(UserFixtures::USER_ADMIN_EMAIL)
    ;
});

it('can get list of draft articles', function (): void {
    $response = $this->getArrayResponseWithNoError(
        url: '/articles/draft',
    );

    expect(count($response))->toBe(1)
        ->and($response[0]['title'])->toBe('Article Draft Fixture')
        ->and($response[0]['content'])->toBeString()
        ->and($response[0]['status'])->toBe(ArticleStatusEnum::DRAFT->value)
        ->and($response[0]['publishedAt'])->not->toBeNull()
        ->and($response[0]['author']['uuid'])->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response[0]['author']['username'])->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response[0]['author']['email'])->toBe(UserFixtures::USER_ADMIN_EMAIL)
    ;
});

it('can get list of articles by author', function (): void {
    $response = $this->getArrayResponseWithNoError(
        url: sprintf('/articles/author/%s', UserFixtures::USER_ADMIN_UUID),
    );

    expect(count($response))->toBe(2)
        ->and($response[0]['title'])->toBe('Article Published Fixture')
        ->and($response[0]['publishedAt'])->not->toBeNull()
        ->and($response[0]['author']['uuid'])->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response[0]['author']['username'])->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response[0]['author']['email'])->toBe(UserFixtures::USER_ADMIN_EMAIL)
        ->and($response[1]['title'])->toBe('Article Draft Fixture')
        ->and($response[1]['author']['uuid'])->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response[1]['author']['username'])->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response[1]['author']['email'])->toBe(UserFixtures::USER_ADMIN_EMAIL)
    ;
});
