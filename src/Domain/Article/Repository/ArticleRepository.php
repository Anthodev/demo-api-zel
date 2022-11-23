<?php

declare(strict_types=1);

namespace App\Domain\Article\Repository;

use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /** @return list<Article> */
    public function findAllPublished(): array
    {
        /** @var list<Article> */
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', ArticleStatusEnum::PUBLISHED->value)
            ->getQuery()
            ->getResult()
        ;
    }

    /** @return list<Article> */
    public function findAllDraft(): array
    {
        /** @var list<Article> */
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', ArticleStatusEnum::DRAFT->value)
            ->getQuery()
            ->getResult()
        ;
    }

    /** @return list<Article> */
    public function findByAuthor(User $user): array
    {
        /** @var list<Article> */
        return $this->createQueryBuilder('a')
            ->andWhere('a.author = :author')
            ->setParameter('author', $user)
            ->getQuery()
            ->getResult()
        ;
    }
}
