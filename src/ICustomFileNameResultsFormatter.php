<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Results formatter that allows customization of file name for results
 *
 * @author Jakub Konečný
 * @internal
 */
interface ICustomFileNameResultsFormatter extends IResultsFormatter
{
    /**
     * Set file name for output that should be used instead of the default one
     */
    public function setOutputFileName(string $baseFileName): void;
}
