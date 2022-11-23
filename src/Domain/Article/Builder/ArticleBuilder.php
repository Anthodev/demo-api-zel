<?php

declare(strict_types=1);

namespace App\Domain\Article\Builder;

use App\Application\Common\Builder\BaseBuilder;
use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Provider\ContextProvider;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\User\Fetcher\UserFetcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ArticleBuilder extends BaseBuilder implements ArticleBaseBuilderInterface
{
    #[Required]
    public ContextProvider $contextProvider;

    #[Required]
    public UserFetcherInterface $userFetcher;

    /**
     * @throws EntityNotFoundException
     */
    public function buildWithDefaultParameters(array $input): Article
    {
        /** @var Article $article */
        $article = $this->build(Article::class, $input);

        if (!isset($input['status'])) {
            $article->setStatus(ArticleStatusEnum::DRAFT->value);
        }

        if (!isset($input['author'])) {
            $article->setAuthor($this->contextProvider->getContextUser());
        } else {
            $author = $this->userFetcher->find($input['author']);

            if (null === $author) {
                throw new EntityNotFoundException('Author not found');
            }

            $article->setAuthor($author);
        }

        if (!isset($input['publishedAt'])) {
            $article->setPublishedAt(null);
        }

        return $article;
    }
}
