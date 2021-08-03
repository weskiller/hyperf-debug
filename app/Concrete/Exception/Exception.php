<?php

declare(strict_types=1);

namespace App\Concrete\Exception;

use Hyperf\Di\Exception\Exception as HyperfException;
use Throwable;

abstract class Exception extends HyperfException
{
    protected int $statusCode = 500;

    /**
     * @param string $message
     * @param int $code
     *
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function create(
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ): self {
        return new static($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
