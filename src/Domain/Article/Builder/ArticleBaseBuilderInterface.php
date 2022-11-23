<?php

declare(strict_types=1);

namespace App\Domain\Article\Builder;

use App\Application\Common\Builder\BaseBuilderInterface;
use App\Domain\Article\Entity\Article;

interface ArticleBaseBuilderInterface extends BaseBuilderInterface
{
    /** @param array<string, mixed> $input */
    public function buildWithDefaultParameters(array $input): Article;
}
