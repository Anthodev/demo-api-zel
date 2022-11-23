<?php

declare(strict_types=1);

namespace App\Domain\Article\Enum;

enum ArticleSerializerGroupsEnum: string
{
    case ARTICLE_READ = 'article:read';

    /**
     * @return list<string>
     */
    public static function toArray(): array
    {
        return [
            self::ARTICLE_READ->value,
        ];
    }
}
