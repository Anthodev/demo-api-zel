<?php

declare(strict_types=1);

namespace App\Domain\Article\Controller;

use App\Application\Common\Controller\AbstractApplicationController;
use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Exception\EntityValidationException;
use App\Application\Common\Exception\InvalidArgumentException;
use App\Application\Common\Manager\BaseManagerInterface;
use App\Domain\Article\Builder\ArticleBaseBuilderInterface;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleSerializerGroupsEnum;
use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\Article\Fetcher\ArticleFetcherInterface;
use App\Domain\Article\Security\ArticleVoter;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractApplicationController
{
    #[Route(
        path: '/article/{uuid}',
        name: 'article_get',
        methods: [Request::METHOD_GET]
    )]
    public function get(
        Article $article,
    ): Response {
        return $this->output($article, groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/articles',
        name: 'articles_list',
        methods: [Request::METHOD_GET]
    )]
    public function list(
        ArticleFetcherInterface $articleFetcher,
    ): Response {
        return $this->output($articleFetcher->findAll(), groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/articles/published',
        name: 'articles_published_list',
        methods: [Request::METHOD_GET]
    )]
    public function listPublished(
        ArticleFetcherInterface $articleFetcher,
    ): Response {
        return $this->output($articleFetcher->findAllPublished(), groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/articles/draft',
        name: 'articles_draft_list',
        methods: [Request::METHOD_GET]
    )]
    public function listDrafts(
        ArticleFetcherInterface $articleFetcher,
    ): Response {
        return $this->output($articleFetcher->findAllDraft(), groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/articles/author/{uuid}',
        name: 'articles_author_list',
        methods: [Request::METHOD_GET]
    )]
    public function listForAuthor(
        User $user,
        ArticleFetcherInterface $articleFetcher,
    ): Response {
        return $this->output($articleFetcher->findByAuthor($user), groups: ArticleSerializerGroupsEnum::toArray());
    }

    /**
     * @throws InvalidArgumentException
     * @throws EntityValidationException
     * @throws \JsonException
     */
    #[Route(
        path: '/article/new',
        name: 'article_new',
        methods: [Request::METHOD_POST]
    )]
    public function create(
        Request $request,
        ArticleBaseBuilderInterface $builder,
        BaseManagerInterface $baseManager,
    ): Response {
        $this->denyAccessUnlessGranted(ArticleVoter::ADD);

        $requestInput = $request->getContent();

        /** @phpstan-ignore-next-line  */
        if (null === $requestInput) {
            throw new InvalidArgumentException('Invalid input');
        }

        /** @var string $requestInput */
        $input = $this->deserialize($requestInput);

        $article = $builder->buildWithDefaultParameters($input);

        $this->denyAccessUnlessGranted(ArticleVoter::ADD, $article);

        $baseManager->insert($article);

        return $this->output($article, groups: ArticleSerializerGroupsEnum::toArray());
    }

    /**
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     * @throws EntityValidationException
     * @throws \JsonException
     */
    #[Route(
        path: '/article/{uuid}/update',
        name: 'article_update',
        methods: [Request::METHOD_PATCH]
    )]
    public function update(
        Article $article,
        Request $request,
        ArticleBaseBuilderInterface $builder,
        BaseManagerInterface $baseManager,
    ): Response {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT);

        $requestInput = $request->getContent();

        /** @phpstan-ignore-next-line  */
        if (null === $requestInput) {
            throw new InvalidArgumentException('Invalid input');
        }

        /** @var string $requestInput */
        $input = $this->deserialize($requestInput);

        if (isset($input['status'])) {
            /** @phpstan-ignore-next-line */
            if (count($input) === 1 && isset($input['status'])) {
                throw new InvalidArgumentException('You cannot update only the status with this endpoint');
            }

            unset($input['status']);
        }

        /** @var Article $article */
        $article = $builder->populate($input, $article);

        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $article);

        $baseManager->update($article);

        return $this->output($article, groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/article/{uuid}/update/draft',
        name: 'article_update_status_draft',
        methods: [Request::METHOD_PATCH]
    )]
    public function updateStatusToDraft(
        Article $article,
        BaseManagerInterface $baseManager,
    ): Response {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT_STATUS);

        $article->setStatus(ArticleStatusEnum::DRAFT->value);

        $this->denyAccessUnlessGranted(ArticleVoter::EDIT_STATUS, $article);

        $baseManager->update($article);

        return $this->output($article, groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/article/{uuid}/update/published',
        name: 'article_update_status_published',
        methods: [Request::METHOD_PATCH]
    )]
    public function updateStatusToPublished(
        Article $article,
        BaseManagerInterface $baseManager,
    ): Response {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT_STATUS);

        $article->setStatus(ArticleStatusEnum::PUBLISHED->value);

        $this->denyAccessUnlessGranted(ArticleVoter::EDIT_STATUS, $article);

        $baseManager->update($article);

        return $this->output($article, groups: ArticleSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/article/{uuid}/update/deleted',
        name: 'article_update_status_deleted',
        methods: [Request::METHOD_DELETE]
    )]
    public function updateStatusToDeleted(
        Article $article,
        BaseManagerInterface $baseManager,
    ): Response {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT_STATUS);

        $article->setStatus(ArticleStatusEnum::DELETED->value);

        $this->denyAccessUnlessGranted(ArticleVoter::EDIT_STATUS, $article);

        $baseManager->delete($article);

        return new JsonResponse();
    }
}
