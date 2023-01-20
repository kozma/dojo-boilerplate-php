<?php

namespace App\Logging;

class NullLogger implements Logger
{
    public function log(string $message): void
    {
    }
}