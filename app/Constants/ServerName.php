<?php


namespace App\Constants;


use Hyperf\Constants\Annotation\Constants;

/**
 * Class ServerName
 *
 * @package App\Constants
 * @Constants()
 */
class ServerName
{
    /**
     * @description("Http服务器")
     */
    public const HttpServer = 'http';

    /**w
     * @description("websocket服务器")
     */
    public const WebSocketServer = 'websocket';
}