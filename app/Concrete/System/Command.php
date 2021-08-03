<?php


namespace App\Concrete\System;


use Psr\Log\LoggerInterface;

class Command
{
    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = logger('command','system');
    }

    public function exec(string $command) :Result
    {
        @exec($command,$outPut,$code);
        $result = new Result($command,$outPut,$code);
        $this->logger->debug((string) $result);
        return $result;
    }
}