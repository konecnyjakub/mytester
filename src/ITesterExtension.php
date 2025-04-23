<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventSubscriber;

/**
 * Extension for {@see Tester}
 *
 * @author Jakub Konečný
 */
interface ITesterExtension extends EventSubscriber
{
    public function getName(): string;
}
