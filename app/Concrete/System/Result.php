<?php


namespace App\Concrete\System;


use Stringable;

class Result implements Stringable
{
    public string $command;

    public array $out;

    public int $code;

    public function __construct(string $command,array $out,int $code)
    {
        $this->command = $command;
        $this->out = $out;
        $this->code = $code;
    }

    /**
     * @return bool
     */
    public function isSuccess() :bool
    {
        return $this->code === 0;
    }

    public function __toString() :string
    {
        $out = implode(PHP_EOL,$this->out);
        return <<<EOF
# {$this->command}
$out
{$this->code}
EOF;
    }
}