<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

enum TypeExceptionEnum: string
{
    case Internal = 'internal';
    case Authorization = 'authorization';
    case Validation = 'validation';
    case NotFound = 'not found';
    case Other = 'other';
}
