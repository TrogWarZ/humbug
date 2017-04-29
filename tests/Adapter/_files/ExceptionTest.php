<?php

use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    /**
     * @group PHPUnitRunnerTesting
     */
    public function testSomeException()
    {
        throw new Exception('exception');
    }
}
