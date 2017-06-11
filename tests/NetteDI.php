<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

$configurator = new Nette\Configurator;
Nette\Utils\FileSystem::createDir(__DIR__ . "/temp");
$configurator->setTempDirectory(__DIR__ . "/temp");
$configurator->addConfig(__DIR__ . "/config.neon");
$container = $configurator->createContainer();
$result = $container->getService("mytester.runner")->execute();
exit((int) $result);
?>