<?php

use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /**
     * @group PHPUnitRunnerTesting
     */
    public function testSomeError()
    {
        trigger_error('error', E_USER_NOTICE);
    }
}
