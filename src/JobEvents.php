<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventSubscriber;

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
        $callback = $event->job->callback;
        if (!is_array($callback) || !isset($callback[0]) || !$callback[0] instanceof TestCase) {
            return;
        }
        /** @var callable&array{0: TestCase, 1: string} $callback */
        if ($callback[0]->shouldCheckAssertions($callback[1]) && $event->job->totalAssertions === 0) {
            echo "Warning: No assertions were performed.\n";
        }
    }
}
