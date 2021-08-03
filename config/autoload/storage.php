<?php
declare(strict_types=1);

return [
    'root' => env('STORAGE_ROOT'),
    'prefix' => env('STORAGE_PREFIX'),
    'tmp' => '/tmp/' . env('APP_NAME'),
    'path' => BASE_PATH . '/storage/resources',
];