<?php

declare(strict_types=1);

namespace App\Domain\Article\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ArticlePublishedDateStatus extends Constraint
{
    public const ARTICLE_PUBLISHED_DATE_STATUS_ERROR_CODE = 'd7fb2707-878e-4b57-81f3-ecb1d4830957';
    public string $message = 'You cannot set a future date for article in published status';

    /**
     * @return string|string[]
     */
    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
