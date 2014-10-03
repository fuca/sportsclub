<?php

use Nette\Forms\Form,
    Vodacek\Forms\Controls\DateInput,
    Doctrine\DBAL\Types\Type;
	

//if (!class_exists('Tester\Assert')) {
//	echo "Install Nette Tester using `composer update --dev`\n";
//	exit(1);
//}
//Tester\Environment::setup();

$composer = require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
//$configurator->setDebugMode(TRUE);  // debug mode MUST NOT be enabled on production server
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

// robot loader se stara o nacitani mych veci a composer autoloader se stara o nacitani knihoven
$robotLoader = $configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../vendor/others')
	->addDirectory(__DIR__)
	->register();
//$composer->addClassMap($robotLoader->getIndexedClasses());
//$configurator->addConfig(__DIR__ . '/config/config.local.neon'); // nahrazeni includem v config.neon
$configurator->addConfig(__DIR__ . '/config/config.neon');

// enum types register for database use
Type::addType("AclMode", "App\Model\Misc\Enum\AclMode");
Type::addType("WebProfileStatus", "App\Model\Misc\Enum\WebProfileStatus");
Type::addType("PrivateMessageStatus", "App\Model\Misc\Enum\PrivateMessageStatus");
Type::addType("PaymentStatus", "App\Model\Misc\Enum\PaymentStatus");
Type::addType("PaymentOwnerType", "App\Model\Misc\Enum\PaymentOwnerType");
Type::addType("FormMode", "App\Model\Misc\Enum\FormMode");
Type::addType("EventVisibility", "App\Model\Misc\Enum\EventVisibility");
Type::addType("EventType", "App\Model\Misc\Enum\EventType");
Type::addType("AclPrivilege", "App\Model\Misc\Enum\AclPrivilege");
Type::addType("ArticleStatus", "App\Model\Misc\Enum\ArticleStatus");
Type::addType("CommentMode", "App\Model\Misc\Enum\CommentMode");


// form extensions
DateInput::register($configurator);
Form::extensionMethod('addImageSelectBox', function(Form $_this, $name, $label = NULL, array $items = NULL, $size = NULL) {
  return $_this[$name] = new RadekDostal\NetteComponents\ImageSelectBox($label, $items, $size);
});

\DependentSelectBox\DependentSelectBox::register();
\DependentSelectBox\JsonDependentSelectBox::register();

// regitration of app modules

//$configurator->onCompile[] = function (Configurator $config, Compiler $compiler) {
//    $compiler->addExtension('usersModule', new \App\UsersModule\Config\UsersModuleExtension());
//};

$container = $configurator->createContainer();
$container->addService('robotLoader', $robotLoader); // tohle tu musi byt skrz PresenterTree

return $container;
