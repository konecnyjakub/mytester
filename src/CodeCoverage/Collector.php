<?php

declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\CodeCoverageException as Exception;

/**
 * Code coverage collector
 *
 * @author Jakub Konečný
 * @internal
 */
final class Collector
{
    /** @var ICodeCoverageEngine[] */
    private array $engines = [];
    private ?ICodeCoverageEngine $currentEngine = null;

    public function registerEngine(ICodeCoverageEngine $engine): void
    {
        $this->engines[] = $engine;
    }

    /**
     * @throws Exception
     */
    public function start(): void
    {
        $engine = $this->selectEngine();
        $engine->start();
    }

    /**
     * @throws Exception
     */
    public function finish(): array
    {
        if ($this->currentEngine === null) {
            throw new Exception("Code coverage collector has not been started.", Exception::COLLECTOR_NOT_STARTED);
        }
        return $this->currentEngine->collect();
    }

    /**
     * @throws Exception
     */
    public function getEngineName(): string
    {
        $engine = $this->selectEngine();
        return $engine->getName();
    }

    /**
     * @throws Exception
     */
    private function selectEngine(): ICodeCoverageEngine
    {
        if ($this->currentEngine !== null) {
            return $this->currentEngine;
        }
        foreach ($this->engines as $engine) {
            if ($engine->isAvailable()) {
                $this->currentEngine = $engine;
                return $engine;
            }
        }
        throw new Exception("No code coverage engine is available.", Exception::NO_ENGINE_AVAILABLE);
    }
}
