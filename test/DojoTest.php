<?php

namespace Test;

use App\Dojo;
use PHPUnit\Framework\TestCase;

class DojoTest extends TestCase
{
    private Dojo $dojo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dojo = new Dojo();
    }

    public function test_shouldReturnTrue(): void
    {
        $this->assertTrue($this->dojo->test());
    }
}
