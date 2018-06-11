<?php

namespace Test;

use App\Dojo;
use PHPUnit\Framework\TestCase;

class DojoTest extends TestCase
{
    /**
     * @var Dojo
     */
    private $dojo;

    protected function setUp()
    {
        parent::setUp();
        $this->dojo = new Dojo();
    }

    public function test_shouldReturnTrue()
    {
        $this->assertTrue($this->dojo->test());
    }
}
