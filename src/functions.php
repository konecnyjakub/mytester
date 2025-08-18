<?php
declare(strict_types=1);

/**
 * @throws Exception
 */
function findVendorDirectory(): string
{
    if (isset($GLOBALS["_composer_autoload_path"]) && is_string($GLOBALS["_composer_autoload_path"])) {
        return dirname($GLOBALS["_composer_autoload_path"]);
    }
    $recursionLimit = 10;
    $findVendor = static function (string $dirName = "vendor/bin", string $dir = __DIR__) use (&$findVendor, &$recursionLimit): string {
        $recursionLimit--;
        if ($recursionLimit === 0) {
            throw new Exception("Cannot find vendor directory.");
        }
        $found = $dir . "/$dirName";
        if (is_dir($found) || is_file($found)) {
            return dirname($found);
        }
        return $findVendor($dirName, dirname($dir));
    };
    return $findVendor();
}
