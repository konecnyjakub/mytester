<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\IEventSubscriber;

/**
 * Results formatter for {@see Tester}
 *
 * @author Jakub Konečný
 */
interface IResultsFormatter extends IEventSubscriber
{
    /**
     * Generates results of Tester run and outputs it to set file/console
     *
     * @param string $outputFolder Where file with output should be created (if writing to a file)
     */
    public function outputResults(string $outputFolder): void;

    /**
     * Returns file name to which result should written. The file does not have to exist yet
     * It can be an absolute path or standard output (or anything accepted by {@see fopen()})
     */
    public function getOutputFileName(string $folder): string;

    /**
     * Set file name for output that should be used instead of the default one
     */
    public function setOutputFileName(string $baseFileName): void;
}
