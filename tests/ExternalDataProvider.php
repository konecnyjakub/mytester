<?php
declare(strict_types=1);

namespace MyTester;

final class ExternalDataProvider
{
    use \Nette\StaticClass;

    public static function dataProviderArray(): array
    {
        return [
            "first" => ["abc", 1, ],
            ["abcd", 2, ],
        ];
    }

    public function dataProviderNonStatic(): array
    {
        return [
            ["abc", 1,  ],
            ["abcd", 2,  ],
        ];
    }
}
