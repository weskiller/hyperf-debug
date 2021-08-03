<?php

declare(strict_types=1);


namespace App\Concrete\Http;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hyperf\Guzzle\RetryMiddleware;
use Hyperf\Utils\Coroutine;

/**
 * Class LogMiddleware.
 *
 * @see RetryMiddleware
 */
class LogMiddleware
{
    public static function afterReceiveResponse(): callable
    {
        return static function (callable $handler) {
            $formatter = new MessageFormatter();
            return static function ($request, array $options) use (
                $handler,
                $formatter
            ) {
                return $handler($request, $options)->then(
                /** @var Request $request */
                /** @var Response $response */
                    static function ($response) use (
                        $request,
                        $formatter,
                        $options
                    ) {
                        self::logger($formatter, $options, $request, $response);
                        return $response;
                    },
                    static function ($reason) use ($formatter, $options) {
                        self::errorLogger($formatter, $options, $reason);
                        return Create::rejectionFor($reason);
                    }
                );
            };
        };
    }

    /**
     * @param $formatter
     * @param $options
     * @param $request
     * @param $response
     */
    public static function logger(
        $formatter,
        $options,
        $request,
        $response
    ): void {
        self::execute(static function () use (
            $formatter,
            $options,
            $request,
            $response
        ) {
            /** @var ClientOption $option */
            $option = data_get($options, ClientOption::class);
            $message = $formatter->format(
                $request,
                $response,
                $option
            );
            if ($option) {
                logger($option->name, $option->group)->debug($message);
            }
        });
    }

    public static function execute(callable $call): void
    {
        if (Coroutine::inCoroutine()) {
            cgo($call, false);
        } else {
            $call();
        }
    }

    /**
     * @param $formatter
     * @param $options
     * @param $reason
     */
    public static function errorLogger($formatter, $options, $reason): void
    {
        self::execute(static function () use ($formatter, $options, $reason) {
            /** @var ClientOption $option */
            $option = data_get($options, ClientOption::class);
            $message = $formatter->error($reason, $option);
            if ($option) {
                logger($option->name, $option->group)->debug($message);
            }
        });
    }
}
