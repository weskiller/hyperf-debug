<?php

declare(strict_types=1);


namespace App\Concrete\Http;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MessageFormatter.
 *
 * @see \GuzzleHttp\MessageFormatter
 */
class MessageFormatter
{
    /** @var string */
    public const End = '<------------------------------------------------------------------------------------------>';

    /** @var string */
    public const Div = '---------------------------------------------';

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ClientOption|null $option
     *
     * @return string
     * @see Client
     */
    public function format(
        RequestInterface $request,
        ResponseInterface $response,
        ?ClientOption $option = null
    ): string {
        return sprintf(
            "<%s> %s\n%s\n%s\n%s\n%s",
            $option->uuid ?? 'null',
            $option ? $option->getTransferTime() : 'NaN',
            Message::toString($request),
            self::Div,
            Message::toString($response),
            self::End
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     * @see LogMiddleware
     */
    public function request(RequestInterface $request): string
    {
        return sprintf("\n%s\n%s", Message::toString($request), self::End);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     * @see LogMiddleware
     */
    public function response(ResponseInterface $response): string
    {
        return sprintf("\n%s\n%s", Message::toString($response), self::End);
    }

    /**
     * @param RequestException $exception
     * @param ClientOption|null $option
     *
     * @return string
     * @see LogMiddleware
     */
    public function error(
        RequestException $exception,
        ?ClientOption $option = null
    ): string {
        $request = $exception->getRequest();
        $response = $exception->getResponse();
        if ($option) {
            return sprintf(
                "<%s> (%s) %s\n%s:%s\n%s\n%s\n%s",
                $option->uuid,
                $exception->getMessage(),
                $response ? last($response->getHeader(RequestOptions::ON_STATS))
                    : '(on_stat)',
                $exception->getFile(),
                $exception->getLine(),
                $request ? Message::toString($request) : '(request)',
                $response ? Message::toString($response) : '(response)',
                self::End
            );
        }

        return sprintf(
            "%s\n%s:%s\n%s\n%s\n%s\n%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $request ? Message::toString($request) : '(request)',
            self::Div,
            $response ? Message::toString($response) : '(response)',
            self::End,
        );
    }
}
