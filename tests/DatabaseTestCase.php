<?php

namespace KdybyTests\DoctrineForms;

use Doctrine\ORM\Tools\SchemaTool;
use Kdyby;
use Nette,
    Nette\Configurator;
use Nette\PhpGenerator as Code;
use Tester;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class DatabaseTestCase extends Tester\TestCase {
    

//	/**
//	 * @var \Nette\DI\Container|\SystemContainer
//	 */
//	protected $serviceLocator;
//	
//	/**
//	 * @return Kdyby\Doctrine\EntityManager
//	 */
//	protected function createMemoryManager() {
//		$rootDir = __DIR__ . '/../../';
//		$config = new Configurator();
//		$container = $config->setTempDirectory(TEMP_DIR)
//			->addConfig(__DIR__ . '/../nette-reset.neon')
//			->addConfig(__DIR__ . '/config/memory.neon')
//			->addParameters(array(
//				'appDir' => $rootDir,
//				'wwwDir' => $rootDir,
//			))
//			->createContainer();
//		/** @var Nette\DI\Container $container */
//		
//		$em = $container->getByType('Kdyby\Doctrine\EntityManager');
//		/** @var Kdyby\Doctrine\EntityManager $em */
//		
//		$schemaTool = new SchemaTool($em);
//		$schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
//		$this->serviceLocator = $container;
//		return $em;
//	}
//	
//	/**
//	 * @param string $className
//	 * @param array $props
//	 * @return object
//	 */
//	protected function newInstance($className, $props = array()) {
//		return Code\Helpers::createObject($className, $props);
//	}

}