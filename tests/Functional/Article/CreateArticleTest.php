<?php

declare(strict_types=1);

namespace Tests\Functional\Article;

use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\Article\Validator\ArticlePublishedDateStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\ByteString;
use Tests\DataFixtures\User\UserFixtures;

beforeEach(function () {
    $this->loadBaseFixturesOnly();
    $this->loginUser();
});

it('can create an article', function (): void {
    $response = $this->getObjectResponseWithNoError(
        data: sprintf(
            '
                {
                    "title": "%s",
                    "content": "%s"
                }
            ',
            'Test title',
            'Test content',
        ),
        method: Request::METHOD_POST,
        url: '/article/new',
    );

    /** @var EntityManagerInterface $entityManager */
    $entityManager = $this->getEntityManager();

    /** @var ?Article $article */
    $article = $entityManager->getRepository(Article::class)->findOneBy(['uuid' => $response->uuid]);

    expect($article)
        ->not()->toBeNull()
        ->and($article->getAuthor()->getUsername())->toBe(UserFixtures::USER_ADMIN_USERNAME)
        ->and($article->getTitle())->toBe('Test title')
        ->and($article->getContent())->toBe('Test content')
        ->and($article->getStatus())->toBe(ArticleStatusEnum::DRAFT->value)
    ;
});

it('cannot create an article without title', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "title": "%s",
                    "content": "%s"
                }
            ',
            ByteString::fromRandom(129)->toString(),
            'Test content',
        ),
        method: Request::METHOD_POST,
        url: '/article/new',
    );

    expect($response->status)
        ->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->detail)->toContain('This value is too long. It should have 128 characters or less.')
    ;
});

it('cannot create an article with a too long title', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "content": "%s"
                }
            ',
            'Test content',
        ),
        method: Request::METHOD_POST,
        url: '/article/new',
    );

    expect($response->status)
        ->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->detail)->toContain('This value should not be blank.')
    ;
});

it('cannot create an article without content', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "title": "%s"
                }
            ',
            'Test title',
        ),
        method: Request::METHOD_POST,
        url: '/article/new',
    );

    expect($response->status)
        ->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->detail)->toContain('This value should not be blank.')
    ;
});

it('cannot create an article with a non existing author', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "title": "%s",
                    "content": "%s",
                    "author": "%s"
                }
            ',
            'Test title',
            'Test content',
            '477d4e58-4387-4147-8f34-b3df7c1f0111'
        ),
        method: Request::METHOD_POST,
        url: '/article/new',
    );

    expect($response->status)
        ->toBe(Response::HTTP_NOT_FOUND)
        ->and($response->detail)->toContain('Author not found')
    ;
});

it('cannot create an article with a future date on published status', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "title": "%s",
                    "content": "%s",
                    "status": "%s",
                    "publishedAt": "%s"
                }
            ',
            'Test title',
            'Test content',
            ArticleStatusEnum::PUBLISHED->value,
            (new \DateTime('+1 day'))->format('Y-m-d H:i:s'),
        ),
        method: Request::METHOD_POST,
        url: '/article/new',
    );

    expect($response->status)
        ->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->detail)
            ->toContain(
                'You cannot set a future date for article in published status',
                ArticlePublishedDateStatus::ARTICLE_PUBLISHED_DATE_STATUS_ERROR_CODE
            )
    ;
});
