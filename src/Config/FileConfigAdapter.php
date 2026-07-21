<?php
declare(strict_types=1);

namespace MyTester\Config;

/**
 * @author Jakub Konečný
 * @internal
 */
final class FileConfigAdapter extends BaseFileConfigAdapter
{
    /** @var list<BaseFileConfigAdapter> */
    private array $adapters = [];

    public function __construct(string $basePath)
    {
        parent::__construct($basePath);
        $this->adapters[] = new NeonFileConfigAdapter($this->basePath);
        $this->adapters[] = new YamlFileConfigAdapter($this->basePath);
    }

    public function getPriority(): int
    {
        return PHP_INT_MAX - 1;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    protected function parseConfig(): array
    {
        $adapters = array_filter(
            $this->adapters,
            static fn (BaseFileConfigAdapter $adapter) => $adapter->isAvailable()
        );
        usort(
            $adapters,
            static fn (BaseFileConfigAdapter $a, BaseFileConfigAdapter $b) => $a->getPriority() <=> $b->getPriority()
        );
        foreach ($adapters as $adapter) {
            $config = $adapter->parseConfig();
            if (count($config) > 0) {
                return $config;
            }
        }
        return [];
    }
}
