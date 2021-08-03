<?php

declare(strict_types=1);


namespace App\Concrete\Http;

use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Guzzle\MiddlewareInterface;
use Hyperf\Guzzle\PoolHandler;
use Swoole\Coroutine;

class HandlerStackFactory extends \Hyperf\Guzzle\HandlerStackFactory
{
    /**
     * @var array
     */
    protected $middlewares = [];

    public function create(
        array $option = [],
        array $middlewares = []
    ): HandlerStack {
        $handler = null;
        $option = array_merge($this->option, $option);
        $middlewares = array_merge($this->middlewares, $middlewares);

        if (Coroutine::getCid() > 0) {
            if ($this->usePoolHandler) {
                $handler = make(PoolHandler::class, [
                    'option' => $option,
                ]);
            } else {
                $handler = new CoroutineHandler();
            }
        }
        $stack = HandlerStack::create($handler);
        $stack->push(LogMiddleware::afterReceiveResponse(),
            'after_receive_response');
        foreach ($middlewares as $key => $middleware) {
            if (is_array($middleware)) {
                [$class, $arguments] = $middleware;
                $middleware = new $class(...$arguments);
            }

            if ($middleware instanceof MiddlewareInterface) {
                $stack->push($middleware->getMiddleware(), $key);
            }
        }

        return $stack;
    }
}
