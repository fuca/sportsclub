<?php

/*
 * ===================================
 *	BOOTSTRAP FILE FOR TESTS
 * ===================================
 */

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();
class_alias('Tester\Assert', 'Assert');
date_default_timezone_set('Europe/Prague');
define('TEMP_DIR', __DIR__ . "/tmp/" . getmypid());
Tester\Helpers::purge(TEMP_DIR);

$configurator = new Nette\Configurator;

//$configurator->setDebugMode(TRUE);  // debug mode MUST NOT be enabled on production server
$configurator->enableDebugger(__DIR__ . '/log');

$configurator->setTempDirectory(__DIR__ . '/temp')
	->addParameters([
		    'appDir' => __DIR__ . "/../app/"]);

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . "/../app/")
	->addDirectory(__DIR__ . '/../vendor/others')
	->register();

$configurator->addConfig(__DIR__ . '/config/tests.local.neon');
$configurator->addConfig(__DIR__ . '/config/config.neon');
//$configurator->addConfig(__DIR__ . '/../app/modules/SystemModule/config/applicationConfig.neon');

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader("class_exists");

$container = $configurator->createContainer();
$em = $container->getByType('Kdyby\Doctrine\EntityManager');
$schemaTool = new SchemaTool($em);
$schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

return $container;
