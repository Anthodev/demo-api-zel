<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BuilderError extends \LogicException implements ApplicationExceptionInterface
{
    /**
     * @param string[] $fields
     */
    public function __construct(string $message = '', private array $fields = [], ?Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getType(): TypeExceptionEnum
    {
        return TypeExceptionEnum::Internal;
    }
}
