<?php

use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Humbug_Test2_AllTests::main');
}

require_once __DIR__.'/MathTest.php';

class Humbug_Test2_AllTests
{
    public static function main()
    {
        TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new TestSuite('Math');

        $suite->addTestSuite('MM2_MathTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD === 'Humbug_Test2_AllTests::main') {
    Humbug_Test2_AllTests::main();
}
