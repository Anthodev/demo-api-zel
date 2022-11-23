<?php

declare(strict_types=1);

namespace App\Domain\Article\Fetcher;

use App\Domain\Article\Entity\Article;
use App\Domain\User\Entity\User;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ArticleFetcherInterface
{
    /** @return list<Article> */
    public function findAllPublished(): array;

    /** @return list<Article> */
    public function findAllDraft(): array;

    /** @return list<Article> */
    public function findByAuthor(User $user): array;
}
