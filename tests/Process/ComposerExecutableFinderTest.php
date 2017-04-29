<?php
/**
 * Humbug
 *
 * @category   Humbug
 * @package    Humbug
 * @copyright  Copyright (c) 2015 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    https://github.com/padraic/humbug/blob/master/LICENSE New BSD License
 */

namespace Humbug\Test\Process;

use Humbug\Process\ComposerExecutableFinder;
use PHPUnit\Framework\TestCase;

class ComposerExecutableFinderTest extends TestCase
{
    public function testFinderCanLocatePhpunitExecutable()
    {
        $finder = new ComposerExecutableFinder();
        $result = $finder->find();
        $this->assertRegExp('%composer(\\.bat|\\.phar)?$%i', $result);
    }
}
