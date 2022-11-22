<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApplicationException extends \Exception
{
    public function __construct(string $message = '', int $code = Response::HTTP_INTERNAL_SERVER_ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
