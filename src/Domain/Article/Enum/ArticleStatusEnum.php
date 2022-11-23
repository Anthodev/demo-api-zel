<?php

declare(strict_types=1);

namespace App\Domain\Article\Enum;

enum ArticleStatusEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case DELETED = 'deleted';
}
