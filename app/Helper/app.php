<?php

declare(strict_types=1);

use Carbon\Carbon;
use Hyperf\Cache\Cache;
use Hyperf\Di\Container;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Str;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

if (!function_exists('di')) {
    function di(): Container
    {
        /* @var Container $di */
        $di = ApplicationContext::getContainer();
        return $di;
    }
}

if (!function_exists('cache')) {
    function cache(): Cache
    {
        /* @var */
        return di()->get(Psr\SimpleCache\CacheInterface::class);
    }
}

if (!function_exists('event')) {
    /**
     * @param object $event
     *
     * @return void
     */
    function event(object $event)
    {
        /* @var */
        di()->get(EventDispatcherInterface::class)
            ->dispatch($event);
    }
}

if (!function_exists('cgo')) {
    function cgo(callable $callable, bool $inherit = true): int
    {
        if ($inherit) {
            $coroutineId = Coroutine::id();
            return Coroutine::create(
                static function () use ($callable, $coroutineId) {
                    if ($coroutineId > 0) {
                        Context::copy($coroutineId);
                    }
                    try {
                        $callable();
                    } catch (Throwable $exception) {
                        logger('cgo')
                            ->debug(
                                sprintf(
                                    "%s\n%s:%s\n%s",
                                    $exception->getMessage(),
                                    $exception->getFile(),
                                    $exception->getLine(),
                                    $exception->getTraceAsString()
                                )
                            );
                    }
                }
            );
        }

        return Coroutine::create(static function () use ($callable) {
            try {
                $callable();
            } catch (Throwable $exception) {
                logger('cgo')
                    ->debug(
                        sprintf(
                            "%s\n%s:%s\n%s",
                            $exception->getMessage(),
                            $exception->getFile(),
                            $exception->getLine(),
                            $exception->getTraceAsString()
                        )
                    );
            }
        });
    }
}

if (!function_exists('now')) {
    /**
     * @param null $tz
     *
     * @return Carbon
     */
    function now($tz = null): Carbon
    {
        return Carbon::now($tz);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = null): string
    {
        if ($path) {
            if (Str::startsWith($path, '/')) {
                return BASE_PATH."{{$path}";
            }
            return BASE_PATH."/{$path}";
        }
        return BASE_PATH;
    }
}

if (!function_exists('logger')) {
    function logger(): LoggerInterface
    {
        $args = func_get_args();
        /** @var LoggerFactory $factory */
        $factory = di()->get(LoggerFactory::class);
        if (count($args) > 0) {
            return $factory->get(...$args);
        }

        return $factory->get();
    }
}

if (!function_exists('redis_key_prefix')) {
    function redis_key_prefix(): string
    {
        static $prefix;
        if ($prefix === null) {
            $prefix = env('APP_NAME') ?: 'UNKNOWN';
        }
        return $prefix;
    }
}

if (!function_exists('array2string')) {
    function array2string(array $data, string $glue = ''): string
    {
        $values = [];
        if ($data && is_array($data)) {
            foreach ($data as $key => $value) {
                $values[] = $key.'='.$value;
            }
        }
        return implode($glue, $values);
    }
}