<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\CodeCoverage\Engines\PcovEngine;
use MyTester\CodeCoverage\Engines\XDebugEngine;
use MyTester\CodeCoverage\Formatters\CoberturaFormatter;
use MyTester\CodeCoverage\Formatters\TextFormatter;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Helper
{
    use \Nette\StaticClass;

    /**
     * @var array<string, class-string<ICodeCoverageEngine>>
     */
    public static array $defaultEngines = [
        "pcov" => PcovEngine::class,
        "xdebug" => XDebugEngine::class,
    ];

    /**
     * @var array<string, class-string<ICodeCoverageFormatter>>
     */
    public static array $availableFormatters = [
        "cobertura" => CoberturaFormatter::class,
        "text" => TextFormatter::class,
    ];
}
