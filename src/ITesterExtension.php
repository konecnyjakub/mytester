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
    public function onExtensionsLoaded(Events\ExtensionsLoaded $event): void;

    public function onTestsStarted(Events\TestsStarted $event): void;

    public function onTestsFinished(Events\TestsFinished $event): void;

    public function onTestCaseStarted(Events\TestCaseStarted $event): void;

    public function onTestCaseFinished(Events\TestCaseFinished $event): void;

    public function getName(): string;
}
