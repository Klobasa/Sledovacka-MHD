<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');

/* on local server load local settings else load production settings */
$local = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1')
|| strpos($_SERVER['REMOTE_ADDR'], '192.168.') === 0;

if($local){
    $configurator->addConfig(__DIR__ . '/config/config.local.neon');
} else {
    $configurator->addConfig(__DIR__ . '/config/config.production.neon');
}


$container = $configurator->createContainer();

return $container;
