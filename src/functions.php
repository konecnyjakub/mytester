<?php
declare(strict_types=1);

/**
 * @throws Exception
 */
function findVendorDirectory(): string
{
    $recursionLimit = 10;
    $findVendor = function ($dirName = "vendor/bin", $dir = __DIR__) use (&$findVendor, &$recursionLimit) {
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
