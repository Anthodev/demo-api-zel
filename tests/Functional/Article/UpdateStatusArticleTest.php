<?php

declare(strict_types=1);

namespace Tests\Functional\Article;

use App\Domain\Article\Enum\ArticleStatusEnum;
use Symfony\Component\HttpFoundation\Request;
use Tests\DataFixtures\Article\UpdateArticle\ArticleFixtures;

beforeEach(function (): void {
    $this->loadAdditionalFixtures(
        [
            $this->dataFixtureDir . '/Article/UpdateArticle/Article.yaml',
        ]
    );

    $this->loginUser();
});

it('can change article status from draft to published', function (): void {
    $response = $this->getObjectResponseWithNoError(
        method: Request::METHOD_PATCH,
        url: sprintf('/article/%s/update/%s', ArticleFixtures::ARTICLE_DRAFT_UUID, ArticleStatusEnum::PUBLISHED->value),
    );

    expect($response->status)->toBe(ArticleStatusEnum::PUBLISHED->value);
});

it('can change article status from published to draft', function (): void {
    $response = $this->getObjectResponseWithNoError(
        method: Request::METHOD_PATCH,
        url: sprintf('/article/%s/update/%s', ArticleFixtures::ARTICLE_PUBLISHED_UUID, ArticleStatusEnum::DRAFT->value),
    );

    expect($response->status)->toBe(ArticleStatusEnum::DRAFT->value);
});
