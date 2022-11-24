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
        url: sprintf('/article/%s', ArticleFixtures::ARTICLE_DRAFT_UUID),
    );

    expect($response)
        ->title->toBe('Test title updated')
        ->content->toBe('Test content updated')
        ->status->toBe(ArticleStatusEnum::DRAFT->value)
        ->publishedAt->not->toBeNull()
        ->author->uuid->toBe(UserFixtures::USER_ADMIN_UUID)
        ->author->username->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->author->email->toBe(UserFixtures::USER_ADMIN_EMAIL)
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
        url: sprintf('/article/%s', ArticleFixtures::ARTICLE_DRAFT_UUID),
    );

    expect($response)
        ->status->toBe(Response::HTTP_BAD_REQUEST)
        ->detail->toBe('You cannot update only the status with this endpoint')
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
        url: sprintf('/article/%s', ArticleFixtures::ARTICLE_PUBLISHED_UUID),
    );

    expect($response)
        ->status->toBe(Response::HTTP_FORBIDDEN)
        ->detail->toBe('Access Denied.')
    ;
});

it('cannot update an article when user logged is not the author', function (): void {
    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

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
        url: sprintf('/article/%s', ArticleFixtures::ARTICLE_DRAFT_UUID),
    );

    expect($response)
        ->status->toBe(Response::HTTP_FORBIDDEN)
        ->detail->toBe('Access Denied.')
    ;
});
