<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

return [
    'default' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/debug.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'mysql' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/mysql.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'pay' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/pay.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'oauth' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/oauth.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'crawler' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/crawler.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'websocket' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/websocket.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'processor' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/processor.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
    'system' => [
        'handler' => [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/logs/system.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => null,
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ],
    ],
];
