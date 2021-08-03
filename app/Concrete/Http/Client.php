<?php

declare(strict_types=1);

namespace App\Concrete\Http;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Header;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Hyperf\Di\Container;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Guzzle\RetryMiddleware;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Str;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use RuntimeException;

/**
 * Class Client.
 *
 * @see ClientFactory
 */
class Client extends GuzzleHttpClient
{
    /** @var string */
    public string  $loggerName = 'http';

    /** @var string */
    public string  $loggerGroup = 'http_client';

    /** @var float */
    public function __construct(array $config = [])
    {
        $config[RequestOptions::ON_STATS] = static function (
            TransferStats $stats
        ) {
            if ($resp = $stats->getResponse()) {
                $resp->withAddedHeader(
                    RequestOptions::ON_STATS,
                    $stats->getTransferTime()
                );
            }
        };
        parent::__construct($config);
    }

    /**
     * @param int $retries
     * @param int $delay
     *
     * @return Client
     * @throws
     */
    public static function retryClient(int $retries = 3, int $delay = 3): Client
    {
        return static::create(
            [],
            static fn(HandlerStack $stack) => $stack->push(make(
                RetryMiddleware::class,
                ['retries' => $retries, 'delay' => $delay]
            )->getMiddleware())
        );
    }

    /**
     * @param array $options
     * @param callable|null $stackHandler
     * @return static
     * @throws
     */
    public static function create(
        array $options = [],
        callable $stackHandler = null
    ) {
        $stack = di()
            ->get(HandlerStackFactory::class)
            ->create();
        if ($stackHandler !== null) {
            $stack = $stackHandler($stack);
        }
        $config = array_replace(['handler' => $stack], $options);
        /** @var Container $container */
        $container = ApplicationContext::getContainer();
        if (method_exists($container, 'make')) {
            return $container->make(static::class, ['config' => $config]);
        }
        return new static($config);
    }

    /**
     * @param PsrResponseInterface|null $response
     * @return array
     * @throws
     */
    public static function toJson(?PsrResponseInterface $response): array
    {
        if ($response && $response->getBody()->getSize()) {
            $charset
                = Header::parse($response->getHeader('content-type'))[0]['charset']
                ?? 'utf-8';
            if (Str::lower($charset) !== 'utf-8') {
                $contents = mb_convert_encoding(
                    (string) $response->getBody(),
                    'utf-8',
                    $charset
                );
            } else {
                $contents = (string) $response->getBody();
            }
            return json_decode($contents,true);
        }
        throw new RuntimeException('parse from invalid response body');
    }

    public function request(
        $method,
        $uri = '',
        array $options = []
    ): PsrResponseInterface {
        $options[ClientOption::class] = new ClientOption(
            $this->loggerName,
            $this->loggerGroup
        );
        return parent::request($method, $uri, $options);
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return $this
     */
    public function setLogger(
        string $name = 'http',
        $group = 'http_client'
    ): Client {
        $this->loggerName = $name;
        $this->loggerGroup = $group;
        return $this;
    }
}
