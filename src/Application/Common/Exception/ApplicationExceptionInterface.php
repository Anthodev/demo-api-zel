<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

interface ApplicationExceptionInterface
{
    public function getType(): TypeExceptionEnum;
}
