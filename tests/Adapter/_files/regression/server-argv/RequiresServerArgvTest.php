<?php

use PHPUnit\Framework\TestCase;

class RequiresServerArgvTest extends TestCase
{
    public function testServerShouldHaveArgv()
    {
        $this->assertArrayHasKey('argv', $_SERVER);
        $this->assertArrayHasKey(0, $_SERVER['argv']);
        $this->assertContains('phpunit', $_SERVER['argv'][0]);
    }

    public function testServerArgvShouldContainPhpunit()
    {
        $this->assertContains('phpunit', $_SERVER['argv'][0]);
    }
}
