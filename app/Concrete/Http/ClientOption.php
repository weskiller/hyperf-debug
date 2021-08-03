<?php

declare(strict_types=1);

namespace App\Concrete\Http;


class ClientOption
{
    /** @var string */
    public string $uuid;

    /** @var float */
    public float $start;

    /** @var string */
    public string $name;

    /** @var string */
    public string $group;

    public function __construct(string $name, string $group)
    {
        $this->uuid = time().'-'.random_int(0,9999999);
        $this->start = microtime(true);
        $this->name = $name;
        $this->group = $group;
    }

    public function getTransferTime(): string
    {
        return (string) (microtime(true) - $this->start);
    }
}
