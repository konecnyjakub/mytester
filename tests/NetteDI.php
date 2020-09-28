#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use MyTester\Bridges\NetteDI\TestsRunner;
use Nette\Configurator;

$configurator = new Configurator();
Nette\Utils\FileSystem::createDir(__DIR__ . "/temp");
$configurator->setTempDirectory(__DIR__ . "/temp");
$configurator->addConfig(__DIR__ . "/config.neon");
$container = $configurator->createContainer();
/** @var TestsRunner $runner */
$runner = $container->getByType(TestsRunner::class);
$result = $runner->execute();
exit((int) $result);
?>