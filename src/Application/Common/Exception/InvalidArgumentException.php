<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvalidArgumentException extends ApplicationException
{
    /**
     * @param string         $message
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }
}
