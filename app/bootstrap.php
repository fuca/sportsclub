<?php

use \Nette\Forms\Form,
    \Vodacek\Forms\Controls\DateInput,
    \Doctrine\DBAL\Types\Type;

$composer = require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setDebugMode(true);
//$configurator->setDebugMode(TRUE);  // debug mode MUST NOT be enabled on production server
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

// robot loader se stara o nacitani mych veci a composer autoloader se stara o nacitani knihoven
$robotLoader = $configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../vendor/others')
	->addDirectory(__DIR__)
	->register();
//$composer->addClassMap($robotLoader->getIndexedClasses());

$configurator->addConfig(__DIR__ . '/modules/SystemModule/config/applicationConfig.local.neon');
$configurator->addConfig(__DIR__ . '/modules/SystemModule/config/applicationConfig.neon');

// enum types registering for database use (MUST BE ACCESSIBLE HERE DUE TO CONSOLE COMMAND USAGE) // this issue has been reported

Type::addType("AclMode", "App\Model\Misc\Enum\AclMode");
Type::addType("AclPrivilege", "App\Model\Misc\Enum\AclPrivilege");

Type::addType("WebProfileStatus", "App\Model\Misc\Enum\WebProfileStatus");

Type::addType("ArticleStatus", "App\Model\Misc\Enum\ArticleStatus");

Type::addType("MailBoxEntryType", "App\Model\Misc\Enum\MailBoxEntryType");

Type::addType("PaymentOwnerType", "App\Model\Misc\Enum\PaymentOwnerType");
Type::addType("PaymentStatus", "App\Model\Misc\Enum\PaymentStatus");

Type::addType("FormMode", "App\Model\Misc\Enum\FormMode");
Type::addType("CommentMode", "App\Model\Misc\Enum\CommentMode");
Type::addType("StaticPageStatus", "App\Model\Misc\Enum\StaticPageStatus");

Type::addType("EventParticipationType", "App\Model\Misc\Enum\EventParticipationType");
Type::addType("EventVisibility", "App\Model\Misc\Enum\EventVisibility");
Type::addType("EventType", "App\Model\Misc\Enum\EventType");

Type::addType("MotivationEntryType", "App\Model\Misc\Enum\MotivationEntryType");

Type::addType("WallPostStatus", "\App\Model\Misc\Enum\WallPostStatus");

// form extensions
DateInput::register($configurator);

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader("class_exists");

$container = $configurator->createContainer();
$container->addService('robotLoader', $robotLoader); // due to presenter tree
return $container;
