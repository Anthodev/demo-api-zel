<?php

declare(strict_types=1);

namespace App\Domain\Article\Validator;

use App\Application\Common\Exception\UnexpectedTypeException;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Enum\ArticleStatusEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ArticlePublishedDateStatusValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     * @throws UnexpectedTypeException
     */
    public function validate(mixed $article, Constraint $constraint): void
    {
        if (!$article instanceof Article) {
            throw new UnexpectedTypeException($article, Article::class);
        }

        if (!$constraint instanceof ArticlePublishedDateStatus) {
            throw new UnexpectedTypeException($constraint, ArticlePublishedDateStatus::class);
        }

        $status = $article->getStatus();
        $publishedAt = $article->getPublishedAt();

        if (null === $publishedAt) {
            return;
        }

        if (
            $status === ArticleStatusEnum::PUBLISHED->value
            && $publishedAt->format('Y-m-d') > (new \DateTime())->format('Y-m-d')
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('article.publishedAt')
                ->setCode(ArticlePublishedDateStatus::ARTICLE_PUBLISHED_DATE_STATUS_ERROR_CODE)
                ->addViolation();
        }
    }
}
