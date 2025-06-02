<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventSubscriber;
use Konecnyjakub\EventDispatcher\Listener;
use ReflectionMethod;

/**
 * Event subscriber for {@see TestCase}
 *
 * @author Jakub Konečný
 */
final class TestCaseEvents implements EventSubscriber
{
    private const string EVENT_BEFORE_TEST_SUITE = "beforeTestSuite";
    private const string EVENT_AFTER_TEST_SUITE = "afterTestSuite";
    private const string EVENT_BEFORE_TEST = "beforeTest";
    private const string EVENT_AFTER_TEST = "afterTest";

    private const array DEFAULT_METHODS = [
        "startUp", "shutDown", "setUp", "tearDown",
    ];

    /** @var array<string, string> */
    private array $eventToAttributeMap = [
        self::EVENT_BEFORE_TEST_SUITE => Attributes\BeforeTestSuite::class,
        self::EVENT_AFTER_TEST_SUITE => Attributes\AfterTestSuite::class,
        self::EVENT_BEFORE_TEST => Attributes\BeforeTest::class,
        self::EVENT_AFTER_TEST => Attributes\AfterTest::class,
    ];

    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\TestSuiteStarted::class => [
                ["onTestSuiteStarted", ],
            ],
            Events\TestSuiteFinished::class => [
                ["onTestSuiteFinished", ],
            ],
            Events\TestStarted::class => [
                ["onTestStarted", ],
            ],
            Events\TestFinished::class => [
                ["onTestFinished", ],
            ],
        ];
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestSuiteStarted(Events\TestSuiteStarted $event): void
    {
        $event->testSuite->startUp(); // @phpstan-ignore method.deprecated
        foreach ($this->getCustomMethods(self::EVENT_BEFORE_TEST_SUITE, $event->testSuite) as $method) {
            [$event->testSuite, $method](); // @phpstan-ignore callable.nonCallable
        }
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestSuiteFinished(Events\TestSuiteFinished $event): void
    {
        $event->testSuite->shutDown(); // @phpstan-ignore method.deprecated
        foreach ($this->getCustomMethods(self::EVENT_AFTER_TEST_SUITE, $event->testSuite) as $method) {
            [$event->testSuite, $method](); // @phpstan-ignore callable.nonCallable
        }
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestStarted(Events\TestStarted $event): void
    {
        $callback = $event->test->callback;
        if (is_array($callback) && isset($callback[0]) && $callback[0] instanceof TestCase) {
            $callback[0]->setUp(); // @phpstan-ignore method.deprecated
            foreach ($this->getCustomMethods(self::EVENT_BEFORE_TEST, $callback[0]) as $method) {
                [$callback[0], $method](); // @phpstan-ignore callable.nonCallable
            }
        }
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestFinished(Events\TestFinished $event): void
    {
        $callback = $event->test->callback;
        if (is_array($callback) && isset($callback[0]) && $callback[0] instanceof TestCase) {
            $callback[0]->tearDown(); // @phpstan-ignore method.deprecated
            foreach ($this->getCustomMethods(self::EVENT_AFTER_TEST, $callback[0]) as $method) {
                [$callback[0], $method](); // @phpstan-ignore callable.nonCallable
            }
        }
    }

    /**
     * @param class-string|object $class
     * @return string[]
     */
    private function getCustomMethods(string $eventName, string|object $class): array
    {
        $methods = get_class_methods($class);
        return array_filter($methods, function (string $method) use ($eventName, $class) {
            $attribute = $this->eventToAttributeMap[$eventName];
            $rm = new ReflectionMethod($class, $method);
            return $rm->isPublic() &&
                !$rm->isStatic() &&
                !in_array($method, self::DEFAULT_METHODS, true) &&
                count($rm->getAttributes($attribute)) > 0;
        });
    }
}
