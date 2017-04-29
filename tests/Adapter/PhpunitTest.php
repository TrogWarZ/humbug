<?php
/**
 * Humbug
 *
 * @category   Humbug
 * @package    Humbug
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2015 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    https://github.com/padraic/humbug/blob/master/LICENSE New BSD License
 */

namespace Humbug\Test\Adapter;

use Humbug\Adapter\Phpunit;
use Humbug\Container;
use PHPUnit\Framework\TestCase;

class PhpunitTest extends TestCase
{
    private $root;
    private $tmpDir;
    private $container;

    public function setUp()
    {
        $this->root = __DIR__ . '/_files';

        $tmpDir = sys_get_temp_dir() . '/' . mt_rand(1000000, 9999999);

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir);
        }

        $this->tmpDir = $tmpDir;

        $this->container = $this->createMock(Container::class);
        $this->container->method('getAdapterOptions')->willReturn([]);
        $this->container->method('getTempDirectory')->willReturn($this->tmpDir);
        $this->container->method('getBootstrap')->willReturn('');
        $this->container->method('getTimeout')->willReturn(1200);
        $this->container->method('getSourceList')->willReturn($this->root);
        $this->container->method('getTestRunDirectory')->willReturn($this->root);
        $this->container->method('getBaseDirectory')->willReturn($this->root);
    }

    public function tearDown()
    {
        $this->container = null;

        if (file_exists($this->tmpDir . '/phpunit.times.humbug.json')) {
            unlink($this->tmpDir . '/phpunit.times.humbug.json');
        }

        if (file_exists($this->tmpDir . '/coverage.humbug.php')) {
            unlink($this->tmpDir . '/coverage.humbug.php');
        }

        if (file_exists($this->tmpDir . '/coverage.humbug.txt')) {
            unlink($this->tmpDir . '/coverage.humbug.txt');
        }

        if (file_exists($this->tmpDir . '/phpunit.humbug.xml')) {
            unlink($this->tmpDir . '/phpunit.humbug.xml');
        }

        if (file_exists($this->tmpDir . '/junit.humbug.xml')) {
            unlink($this->tmpDir . '/junit.humbug.xml');
        }

        if (file_exists($this->tmpDir)) {
            rmdir($this->tmpDir);
        }
    }

    /**
     * @group baserun
     */
    public function testAdapterRunsDefaultPhpunitCommand()
    {
        $this->container->method('getSourceList')->willReturn(__DIR__ . '/_files/phpunit');
        $this->container->method('getTestRunDirectory')->willReturn(__DIR__ . '/_files/phpunit');
        $this->container->method('getBaseDirectory')->willReturn(__DIR__ . '/_files/phpunit');
        $this->container->method('getAdapterConstraints')->willReturn('MM1_MathTest MathTest.php');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertContains('##teamcity[', $result, $process->getErrorOutput());
        $this->assertTrue($adapter->ok($result));
    }

    public function testAdapterRunsPhpunitCommandWithAllTestsFileTarget()
    {
        $this->container->method('getSourceList')->willReturn(__DIR__ . '/_files/phpunit2');
        $this->container->method('getTestRunDirectory')->willReturn(__DIR__ . '/_files/phpunit2');
        $this->container->method('getBaseDirectory')->willReturn(__DIR__ . '/_files/phpunit2');
        $this->container->method('getAdapterConstraints')->willReturn('AllTests.php');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertTrue($adapter->ok($result));
    }

    public function testAdapterDetectsTestsPassing()
    {
        $this->container->method('getAdapterConstraints')->willReturn('PassTest');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertTrue($adapter->ok($result), $process->getErrorOutput());
    }

    public function testAdapterDetectsTestsFailingFromTestFail()
    {
        $this->container->method('getAdapterConstraints')->willReturn('FailTest');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertContains('##teamcity[', $result);
        $this->assertFalse($adapter->ok($result), $process->getErrorOutput());
    }

    public function testAdapterDetectsTestsFailingFromException()
    {
        $this->container->method('getAdapterConstraints')->willReturn('ExceptionTest');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertContains('##teamcity[', $result);
        $this->assertFalse($adapter->ok($result), $process->getErrorOutput());
    }

    public function testAdapterDetectsTestsFailingFromError()
    {
        $this->container->method('getAdapterConstraints')->willReturn('ErrorTest');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertContains('##teamcity[', $result);
        $this->assertFalse($adapter->ok($result), $process->getErrorOutput());
    }

    public function testAdapterOutputProcessingDetectsFailOverMultipleLinesWithNoDepOnFinalStatusReport()
    {
        $this->markTestIncomplete('This seems redundant as it should never happen - fail on first failure is set');
        $adapter = new Phpunit;

        $output = <<<OUTPUT
TAP version 13
not ok 1 - Error: Humbug\Adapter\PhpunitTest::testAdapterRunsDefaultPhpunitCommand
ok 78 - Humbug\Test\Mutator\ConditionalBoundary\LessThanOrEqualToTest::testReturnsTokenEquivalentToLessThanOrEqualTo
ok 79 - Humbug\Test\Mutator\ConditionalBoundary\LessThanOrEqualToTest::testMutatesLessThanToLessThanOrEqualTo
ok 80 - Humbug\Test\Mutator\ConditionalBoundary\LessThanTest::testReturnsTokenEquivalentToLessThanOrEqualTo
ok 81 - Humbug\Test\Mutator\ConditionalBoundary\LessThanTest::testMutatesLessThanToLessThanOrEqualTo
not ok 103 - Error: Humbug\Test\Utility\TestTimeAnalyserTest::testAnalysisOfJunitLogFormatShowsLeastTimeTestCaseFirst
1..103

OUTPUT;
        $this->assertFalse($adapter->ok($output));
    }

    /**
     * @dataProvider directoriesList
     */
    public function testShouldNotNotifyRegressionWhileRunningProcess($directory)
    {
        $this->container->method('getSourceList')->willReturn($directory);
        $this->container->method('getTestRunDirectory')->willReturn($directory);
        $this->container->method('getBaseDirectory')->willReturn($directory);
        $this->container->method('getAdapterConstraints')->willReturn('');

        $adapter = new Phpunit();
        $process = $adapter->getProcess($this->container, true, true);
        $process->run();

        $result = $process->getOutput();

        $this->assertEquals(2, $adapter->hasOks($result), $process->getErrorOutput());
        $this->assertContains('##teamcity[', $result);
        $this->assertTrue($adapter->ok($result), "Regression output: \n" . $result);
    }

    public function directoriesList()
    {
        return [
            [__DIR__ . '/_files/regression/wildcard-dirs'],
            ['tests/Adapter/_files/regression/wildcard-dirs'],
            [__DIR__ . '/_files/regression/server-argv'],
        ];
    }
}
