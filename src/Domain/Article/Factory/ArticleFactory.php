<?php

declare(strict_types=1);

namespace App\Domain\Article\Factory;

use App\Application\Common\Factory\FactoryInterface;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\User\Entity\User;
use App\Domain\User\Factory\UserFactory;
use DateTimeInterface;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class ArticleFactory implements FactoryInterface
{
    public static function create(array $input): Article
    {
        $faker = Factory::create();

        /** @var string $title */
        $title = $input['title'] ?? $faker->sentence();

        /** @var string $content */
        $content = $input['content'] ?? $faker->paragraph();

        /** @var User $author */
        $author = $input['author'] ?? UserFactory::create([]);

        /** @var string $status */
        $status = $input['status'] ?? ArticleStatusEnum::DRAFT->value;

        /** @var DateTimeInterface $publishedAt */
        $publishedAt = $input['publishedAt'] ?? $faker->dateTime();

        $article = new Article();

        $article->setTitle($title);

        $article->setContent($content);

        $article->setAuthor($author);

        $article->setStatus($status);

        $article->setPublishedAt($publishedAt);

        if (!isset($input['uuid'])) {
            $uuid = Uuid::v4();

            $article->setUuid($uuid);
        }

        return $article;
    }
}
