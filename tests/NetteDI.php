#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../src/functions.php";

use MyTester\Bridges\NetteDI\ContainerSuiteFactory;
use Nette\Bootstrap\Configurator;

$configurator = new Configurator();
$configurator->setDebugMode(true);
Nette\Utils\FileSystem::createDir(__DIR__ . "/temp");
$configurator->setTempDirectory(__DIR__ . "/temp");
$configurator->addConfig(__DIR__ . "/config.neon");
$container = $configurator->createContainer();
/** @var MyTester\Tester $runner */
$runner = $container->getByType(MyTester\Tester::class);
assert($runner->testSuiteFactory instanceof ContainerSuiteFactory, "Test suite factory is not a " . ContainerSuiteFactory::class . " instance.");
$runner->execute();
