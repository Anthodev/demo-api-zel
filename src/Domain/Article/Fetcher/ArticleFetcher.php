<?php

declare(strict_types=1);

namespace App\Domain\Article\Fetcher;

use App\Application\Common\Fetcher\AbstractFetcher;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Repository\ArticleRepository;
use App\Domain\User\Entity\User;

class ArticleFetcher extends AbstractFetcher implements ArticleFetcherInterface
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {
        parent::__construct($articleRepository);
    }

    /** @return list<Article> */
    public function findAllPublished(): array
    {
        return $this->articleRepository->findAllPublished();
    }

    /** @return list<Article> */
    public function findAllDraft(): array
    {
        return $this->articleRepository->findAllDraft();
    }

    /** @return list<Article> */
    public function findByAuthor(User $user): array
    {
        return $this->articleRepository->findByAuthor($user);
    }
}
