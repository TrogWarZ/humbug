<?php

use PHPUnit\Framework\TestCase;

class FailTest extends TestCase
{
    /**
     * @group PHPUnitRunnerTesting
     */
    public function testSomeFail()
    {
        $this->assertTrue(false);
    }
}
