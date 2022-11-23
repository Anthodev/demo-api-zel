<?php

declare(strict_types=1);

namespace Tests\Functional\Article;

use App\Domain\Article\Enum\ArticleStatusEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

it('can update an article', function (): void {
    $response = $this->getObjectResponseWithNoError(
        data: sprintf(
            '
                {
                    "title": "%s",
                    "content": "%s"
                }
            ',
            'Test title updated',
            'Test content updated',
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/article/%s/update', ArticleFixtures::ARTICLE_DRAFT_UUID),
    );

    expect($response->title)->toBe('Test title updated')
        ->and($response->content)->toBe('Test content updated')
        ->and($response->status)->toBe(ArticleStatusEnum::DRAFT->value)
        ->and($response->publishedAt)->not->toBeNull()
        ->and($response->author->uuid)->toBe(UserFixtures::USER_ADMIN_UUID)
        ->and($response->author->username)->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($response->author->email)->toBe(UserFixtures::USER_ADMIN_EMAIL)
    ;
});

it('cannot update the status of an article', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "status": "%s"
                }
            ',
            ArticleStatusEnum::PUBLISHED->value,
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/article/%s/update', ArticleFixtures::ARTICLE_DRAFT_UUID),
    );

    expect($response->status)->toBe(Response::HTTP_BAD_REQUEST)
        ->and($response->detail)->toBe('You cannot update only the status with this endpoint')
    ;
});

it('cannot update an article in published status', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "title": "%s",
                    "content": "%s"
                }
            ',
            'Test title updated',
            'Test content updated',
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/article/%s/update', ArticleFixtures::ARTICLE_PUBLISHED_UUID),
    );

    expect($response->status)->toBe(Response::HTTP_FORBIDDEN)
        ->and($response->detail)->toBe('Access Denied.')
    ;
});
