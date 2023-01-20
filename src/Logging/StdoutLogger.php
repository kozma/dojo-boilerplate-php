<?php

namespace App\Logging;

class StdoutLogger implements Logger
{
    public function log(string $message): void
    {
        echo "$message\n";
    }
}