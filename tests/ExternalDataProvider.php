<?php
declare(strict_types=1);

namespace MyTester;

final class ExternalDataProvider
{
    use \Nette\StaticClass;

    /**
     * @return array<int|string, array{0: string, 1: int}>
     */
    public static function dataProviderArray(): array
    {
        return [
            "first" => ["abc", 1, ],
            ["abcd", 2, ],
        ];
    }

    /**
     * @return array{0: string, 1: int}[]
     */
    public function dataProviderNonStatic(): array
    {
        return [
            ["abc", 1,  ],
            ["abcd", 2,  ],
        ];
    }
}
