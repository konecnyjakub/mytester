#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use MyTester\Bridges\NetteDI\ContainerSuiteFactory;
use MyTester\Tester;
use Nette\Configurator;

$configurator = new Configurator();
Nette\Utils\FileSystem::createDir(__DIR__ . "/temp");
$configurator->setTempDirectory(__DIR__ . "/temp");
$configurator->addConfig(__DIR__ . "/config.neon");
$container = $configurator->createContainer();
/** @var Tester $runner */
$runner = $container->getByType(Tester::class);
assert($runner->testSuiteFactory instanceof ContainerSuiteFactory, "Test suite factory is not a " . ContainerSuiteFactory::class . " instance.");
$runner->execute();
