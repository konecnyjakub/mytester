<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Helper
{
    use \Nette\StaticClass;

    public static array $defaultEngines = [
        "pcov" => PcovEngine::class,
        "xdebug" => XDebugEngine::class,
    ];

    public static array $availableFormatters = [
        "cobertura" => CoberturaFormatter::class,
        "text" => TextFormatter::class,
    ];
}
