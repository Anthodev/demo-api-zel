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
});

it('can change article status from published to deleted', function (): void {
    $this->loginUser();

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/article/%s/%s', ArticleFixtures::ARTICLE_PUBLISHED_UUID, ArticleStatusEnum::DELETED->value),
    );
});

it('can change article status from draft to deleted', function (): void {
    $this->loginUser();

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/article/%s/%s', ArticleFixtures::ARTICLE_DRAFT_UUID, ArticleStatusEnum::DELETED->value),
    );
});

it('can delete an article when im the author', function (): void {
    $this->loginUser();

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/article/%s/%s', ArticleFixtures::ARTICLE_DRAFT_UUID, ArticleStatusEnum::DELETED->value),
    );
});

it('can delete an article when im the author as normal user', function (): void {
    $this->loadAdditionalFixtures(
        [
            $this->dataFixtureDir . '/Article/UpdateArticle/Article.yaml',
            __DIR__ . '/DataFixtures/DeleteArticle/DeleteArticleFromSameNormalUser.yaml',
        ]
    );

    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/article/%s/%s', '450eb992-4949-421d-85a9-da0bb705f234', ArticleStatusEnum::DELETED->value),
    );
});

it('cannot delete an article from another user', function (): void {
    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

    $response = $this->getObjectResponseWithError(
        method: Request::METHOD_DELETE,
        url: sprintf('/article/%s/%s', ArticleFixtures::ARTICLE_DRAFT_UUID, ArticleStatusEnum::DELETED->value),
    );

    expect($response)
        ->status->toBe(Response::HTTP_FORBIDDEN)
        ->detail->toBe('Access Denied.')
    ;
});
