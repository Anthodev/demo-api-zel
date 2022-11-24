<?php

declare(strict_types=1);

namespace App\Domain\Article\Security;

use App\Application\Common\Helper\EntityInterfaceHelper;
use App\Application\Common\Security\AbstractVoter;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ArticleVoter extends AbstractVoter
{
    #[Required]
    public EntityManagerInterface $entityManager;

    public const ADD = 'add-article';
    public const EDIT = 'edit-article';
    public const EDIT_STATUS = 'edit-article-status';
    public const DELETE = 'delete-article';

    protected string $entityClass = Article::class;

    public function __construct()
    {
        $this->attributes = [
            self::ADD,
            self::EDIT,
            self::EDIT_STATUS,
            self::DELETE,
        ];
    }

    public function canAddArticle(?Article $subject, User $user): bool
    {
        return $this->contextProvider->getContextUserRoleCode() === RoleCodeEnum::ROLE_ADMIN->value;
    }

    public function canEditArticle(?Article $subject, User $user): bool
    {
        $isAuthorized = $this->contextProvider->getContextUserRoleCode() === RoleCodeEnum::ROLE_ADMIN->value;

        if (null === $subject) {
            return $isAuthorized;
        }

        return
            $isAuthorized
            && $subject->getStatus() === ArticleStatusEnum::DRAFT->value
        ;
    }

    public function canEditArticleStatus(?Article $subject, User $user): bool
    {
        $isAuthorized = $this->contextProvider->getContextUserRoleCode() === RoleCodeEnum::ROLE_ADMIN->value;

        if (null === $subject) {
            return $isAuthorized;
        }

        $previousStatus = $this->entityManager->getUnitOfWork()->getOriginalEntityData($subject)['status'];

        return
            $isAuthorized
            && (
                $previousStatus === ArticleStatusEnum::DRAFT->value
                || $previousStatus === ArticleStatusEnum::PUBLISHED->value
            )
        ;
    }

    public function canDeleteArticle(Article $subject, User $user): bool
    {
        return
            $this->contextProvider->getContextUserRoleCode() === RoleCodeEnum::ROLE_ADMIN->value
            || EntityInterfaceHelper::areTheSame($subject->getAuthor(), $user)
        ;
    }
}
