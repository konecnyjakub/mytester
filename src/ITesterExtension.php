<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\IEventSubscriber;

/**
 * Extension for {@see Tester}
 *
 * @author Jakub Konečný
 */
interface ITesterExtension extends IEventSubscriber
{
    public function getName(): string;
}
