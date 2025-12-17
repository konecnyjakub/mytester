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
final readonly class AnnotationsDataProvider implements DataProvider
{
    public const string ANNOTATION_NAME = "dataProvider";

    public const string ANNOTATION_EXTERNAL_NAME = "dataProviderExternal";

    public const string ANNOTATION_SIMPLE_NAME = "data";

    public function __construct(private Reader $annotationsReader)
    {
    }

    /**
     * @inheritDoc
     * @throws InvalidDataProviderException
     * @throws \ReflectionException
     */
    public function getData(object $class, string $method): iterable
    {
        $reflection = new ReflectionMethod($class, $method);
        if ($reflection->getNumberOfParameters() < 1) {
            return [];
        }

        $dataSets = $this->getSimpleDataSets($reflection);
        if ($dataSets !== []) {
            return $dataSets;
        }

        $dataSets = $this->getNormalDataSets($reflection, $class);
        if ($dataSets !== []) {
            return $dataSets;
        }

        $dataSets = $this->getExternalDataSets($reflection);
        if ($dataSets !== []) {
            return $dataSets;
        }

        return [];
    }

    /**
     * @return iterable<string|int, mixed>[] Values for consecutive calls of the the method
     */
    private function getSimpleDataSets(ReflectionMethod $reflection): iterable
    {
        $class = $reflection->class;
        $method = $reflection->name;
        if (!$this->annotationsReader->hasAnnotation(self::ANNOTATION_SIMPLE_NAME, $class, $method)) {
            return [];
        }
        /** @var array<string|int, mixed>[] $result */
        $result = $this->annotationsReader->getAnnotationMulti(self::ANNOTATION_SIMPLE_NAME, $class, $method);
        return $result;
    }

    /**
     * @return iterable<string|int, mixed>[] Values for consecutive calls of the the method
     * @throws InvalidDataProviderException
     */
    private function getNormalDataSets(ReflectionMethod $reflection, object $object): iterable
    {
        $class = $reflection->class;
        $method = $reflection->name;
        $dataProvider = $this->annotationsReader->getAnnotation(self::ANNOTATION_NAME, $class, $method);
        if (!is_string($dataProvider)) {
            return [];
        }
        $className = $reflection->getDeclaringClass()->getName();
        try {
            $reflection = new ReflectionMethod($class, $dataProvider);
            if (!$reflection->isPublic()) {
                throw new InvalidDataProviderException("Method $className::$dataProvider is not public.");
            }
            /** @var callable $callback */
            $callback = [$object, $dataProvider];
            $result = call_user_func($callback);
            if (!is_iterable($result)) {
                throw new InvalidDataProviderException(
                    "Method $className::$dataProvider has to return an array or an iterable object."
                );
            }
            /** @var iterable<string|int, mixed>[] $result */
            return $result;
        } catch (\ReflectionException $e) {
            throw new InvalidDataProviderException("Method $className::$dataProvider does not exist.", 0, $e);
        }
    }

    /**
     * @return iterable<string|int, mixed>[] Values for consecutive calls of the the method
     * @throws InvalidDataProviderException
     */
    private function getExternalDataSets(ReflectionMethod $reflection): iterable
    {
        $class = $reflection->class;
        $method = $reflection->name;
        $dataProviderExternal = $this->annotationsReader->getAnnotation(
            self::ANNOTATION_EXTERNAL_NAME,
            $class,
            $method
        );
        if (!is_string($dataProviderExternal)) {
            return [];
        }
        try {
            [$className, $methodName] = explode("::", $dataProviderExternal);
            $reflection = new ReflectionMethod($className, $methodName);
            if (!$reflection->isPublic()) {
                throw new InvalidDataProviderException("Method $dataProviderExternal is not public.");
            }
            if (!$reflection->isStatic()) {
                throw new InvalidDataProviderException("Method $dataProviderExternal is not static.");
            }
            /** @var string&callable $dataProviderExternal */
            $result = call_user_func($dataProviderExternal);
            if (!is_iterable($result)) {
                throw new InvalidDataProviderException(
                    "Method $dataProviderExternal has to return an array or an iterable object."
                );
            }
            /** @var iterable<string|int, mixed>[] $result */
            return $result;
        } catch (\ReflectionException $e) {
            throw new InvalidDataProviderException("Method $dataProviderExternal does not exist.", 0, $e);
        }
    }
}
