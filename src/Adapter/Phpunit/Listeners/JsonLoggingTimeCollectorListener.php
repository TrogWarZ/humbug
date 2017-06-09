<?php

namespace Humbug\Adapter\Phpunit\Listeners;

use Humbug\Phpunit\Listener\TimeCollectorListener;
use Humbug\Phpunit\Logger\JsonLogger;
use Humbug\Phpunit\Writer\JsonWriter;

class JsonLoggingTimeCollectorListener extends TimeCollectorListener
{
    public function __construct($logFile, $rootSuiteNestingLevel = 0)
    {
        $writer = new JsonWriter($logFile); // TODO investigate how to extract this
        $logger = new JsonLogger($writer);

        parent::__construct($logger, $rootSuiteNestingLevel);
    }
}
