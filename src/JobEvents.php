<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventSubscriber;
use Nette\Utils\Strings;

final class JobEvents implements EventSubscriber
{
    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\TestJobFinished::class => [
                ["checkAssertions", ],
            ],
        ];
    }

    public function checkAssertions(Events\TestJobFinished $event): void
    {
        $rf = $event->job->getCallbackReflection();
        if ($rf === null || !$rf->getClosureThis() instanceof TestCase) {
            return;
        }
        $methodName = $rf->name;
        if (str_starts_with($methodName, "{closure:")) {
            $methodName = str_replace("{closure:" . $rf->getClosureThis()::class . "::", "", $methodName);
            $methodName = (string) Strings::before($methodName, "()");
        }
        if ($rf->getClosureThis()->shouldCheckAssertions($methodName) && $event->job->totalAssertions === 0) {
            echo "Warning: No assertions were performed.\n";
        }
    }
}
