<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use ReflectionMethod;

/**
 * DataProvider
 *
 * @author Jakub Konečný
 * @internal
 */
final class DataProvider
{
    use \Nette\SmartObject;

    public const ANNOTATION_NAME = "dataProvider";

    private Reader $annotationsReader;

    public function __construct(Reader $annotationsReader)
    {
        $this->annotationsReader = $annotationsReader;
    }

    /**
     * @throws InvalidDataProviderException
     * @throws \ReflectionException
     */
    public function getData(object $class, string $method): array
    {
        $reflection = new ReflectionMethod($class, $method);
        if ($reflection->getNumberOfParameters() < 1) {
            return [];
        }
        $dataProvider = $this->annotationsReader->getAnnotation(static::ANNOTATION_NAME, $class, $method);
        if (is_string($dataProvider)) {
            $className = $reflection->getDeclaringClass()->getName();
            try {
                $reflection = new ReflectionMethod($class, $dataProvider);
                if (!$reflection->isPublic()) {
                    throw new InvalidDataProviderException("Method $className::$dataProvider is not public.");
                }
                $result = call_user_func([$class, $dataProvider]); // @phpstan-ignore argument.type
                if (!is_array($result)) {
                    throw new InvalidDataProviderException("Method $className::$dataProvider has to return an array.");
                }
                return $result;
            } catch (\ReflectionException $e) {
                throw new InvalidDataProviderException("Method $className::$dataProvider does not exist.", 0, $e);
            }
        }
        return [];
    }
}
