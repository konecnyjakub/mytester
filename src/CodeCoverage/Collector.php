<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\CodeCoverage\CodeCoverageException as Exception;

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
    private ?Report $report = null;
    /** @var ICodeCoverageFormatter[] */
    private array $formatters = [];

    public function registerEngine(ICodeCoverageEngine $engine): void
    {
        $this->engines[] = $engine;
    }

    public function registerFormatter(ICodeCoverageFormatter $formatter): void
    {
        $this->formatters[] = $formatter;
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
    public function finish(): Report
    {
        if ($this->report === null) {
            if ($this->currentEngine === null) {
                throw new Exception("Code coverage collector has not been started.", Exception::COLLECTOR_NOT_STARTED);
            }

            $data = $this->currentEngine->collect();
            $this->report = new Report($data);
        }

        return $this->report;
    }

    /**
     * @throws Exception
     */
    public function write(string $outputFolder): void
    {
        $this->finish();
        /** @var Report $report */
        $report = $this->report;
        foreach ($this->formatters as $formatter) {
            /** @var resource $outputFile */
            $outputFile = fopen($formatter->getOutputFileName($outputFolder), "w");
            fwrite($outputFile, $formatter->render($report));
            fclose($outputFile);
        }
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
