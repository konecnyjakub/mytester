<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Extension for {@see Tester}
 */
interface ITesterExtension
{
    /**
     * @return callable[]
     */
    public function getEventsPreRun(): array;

    /**
     * @return callable[]
     */
    public function getEventsAfterRun(): array;
}
