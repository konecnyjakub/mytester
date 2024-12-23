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
     * @throws InvalidDataProviderException If the provided source does not exist, is not accessible or does not return array
     * @throws \ReflectionException If the specified test method does not exist
     */
    public function getData(object $class, string $method): array
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
                if (!is_array($result)) {
                    throw new InvalidDataProviderException("Method $className::$dataProvider has to return an array.");
                }
                /** @var array[] $result */
                return $result;
            } catch (\ReflectionException $e) {
                throw new InvalidDataProviderException("Method $className::$dataProvider does not exist.", 0, $e);
            }
        }
        return [];
    }
}
