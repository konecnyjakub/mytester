<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use ReflectionMethod;

/**
 * Default data provider for {@see Tester}
 *
 * @author Jakub Konečný
 */
final readonly class DataProvider implements IDataProvider
{
    public const string ANNOTATION_NAME = "dataProvider";

    public function __construct(private Reader $annotationsReader)
    {
    }

    /**
     * @throws InvalidDataProviderException
     * @throws \ReflectionException
     */
    public function getData(object $class, string $method): iterable
    {
        $reflection = new ReflectionMethod($class, $method);
        if ($reflection->getNumberOfParameters() < 1) {
            return [];
        }
        $dataProvider = $this->annotationsReader->getAnnotation(self::ANNOTATION_NAME, $class, $method);
        if (is_string($dataProvider)) {
            $className = $reflection->getDeclaringClass()->getName();
            try {
                $reflection = new ReflectionMethod($class, $dataProvider);
                if (!$reflection->isPublic()) {
                    throw new InvalidDataProviderException("Method $className::$dataProvider is not public.");
                }
                /** @var callable $callback */
                $callback = [$class, $dataProvider];
                $result = call_user_func($callback);
                if (!is_iterable($result)) {
                    throw new InvalidDataProviderException(
                        "Method $className::$dataProvider has to return an array or an iterable object."
                    );
                }
                /** @var iterable[] $result */
                return $result;
            } catch (\ReflectionException $e) {
                throw new InvalidDataProviderException("Method $className::$dataProvider does not exist.", 0, $e);
            }
        }
        return [];
    }
}
