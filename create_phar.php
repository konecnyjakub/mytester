<?php
$phar = new Phar("mytester.phar");

$phar->buildFromDirectory("src");

$phar->setStub("<?php
Phar::mapPhar('mytester.phar');
require 'phar://mytester.phar/bootstrap.php';
__HALT_COMPILER();");

$phar->compressFiles(Phar::GZ);
?>