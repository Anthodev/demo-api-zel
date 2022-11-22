<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Doctrine\ORM\EntityNotFoundException as DoctrineEntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EntityNotFoundException extends DoctrineEntityNotFoundException
{
    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $previous);
    }
}
