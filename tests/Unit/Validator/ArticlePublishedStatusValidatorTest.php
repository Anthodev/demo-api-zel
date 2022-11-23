<?php

declare(strict_types=1);

namespace Tests\Unit\Validator;

use App\Domain\Article\Enum\ArticleStatusEnum;
use App\Domain\Article\Factory\ArticleFactory;
use App\Domain\Article\Validator\ArticlePublishedDateStatus;
use App\Domain\Article\Validator\ArticlePublishedDateStatusValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ArticlePublishedStatusValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ArticlePublishedDateStatusValidator
    {
        return new ArticlePublishedDateStatusValidator();
    }

    public function testCannotSetFuturePublishedDateForPublishedArticle(): void
    {
        $article = ArticleFactory::create([
            'status' => ArticleStatusEnum::PUBLISHED->value,
            'publishedAt' => new \DateTime('+1 day'),
        ]);

        $this->constraint = new ArticlePublishedDateStatus();

        $this->validator->validate($article, $this->constraint);
        $violations = $this->context->getViolations();

        self::assertCount(1, $violations);

        self::assertSame(
            ArticlePublishedDateStatus::ARTICLE_PUBLISHED_DATE_STATUS_ERROR_CODE,
            $violations->get(0)->getCode()
        );

        self::assertSame(
            'property.path.article.publishedAt',
            $violations->get(0)->getPropertyPath()
        );

        self::assertSame(
            'You cannot set a future date for article in published status',
            $violations->get(0)->getMessage()
        );
    }
}
