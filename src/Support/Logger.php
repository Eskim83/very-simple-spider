<?php

namespace VerySimpleSpider\Support;

class Logger
{
    protected bool $enabled;

    public function __construct(bool $enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function log(string $message): void
    {
        if ($this->enabled) {
            echo "[Logger] $message\n";
        }
    }
}
