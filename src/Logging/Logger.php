<?php

namespace App\Logging;

interface Logger
{
    public function log(string $message): void;
}