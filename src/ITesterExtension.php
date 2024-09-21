<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Extension for {@see Tester}
 *
 * @author Jakub Konečný
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

    /**
     * @return callable[]
     */
    public function getEventsBeforeTestCase(): array;

    /**
     * @return callable[]
     */
    public function getEventsAfterTestCase(): array;
}
