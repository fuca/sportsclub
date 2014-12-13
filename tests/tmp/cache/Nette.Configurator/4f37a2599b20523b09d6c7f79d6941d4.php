<?php
// source: /home/fuca/Projects/www/sportsclub/tests/config/tests.local.neon 
// source: /home/fuca/Projects/www/sportsclub/tests/../app/modules/SystemModule/config/applicationConfig.neon 

/**
 * @property Nette\Application\Application $application
 * @property Tomaj\Image\ImageService $articleImageService
 * @property App\SecurityModule\Model\Authenticator $authenticator
 * @property Nette\Caching\Storages\FileStorage $cacheStorage
 * @property Nette\DI\Container $container
 * @property Nette\Http\Request $httpRequest
 * @property Nette\Http\Response $httpResponse
 * @property Tomaj\Image\Backend\FileBackend $imagesBackend
 * @property Nette\Bridges\Framework\NetteAccessor $nette
 * @property Kdyby\PresenterTree $presenterTree
 * @property Nette\Loaders\RobotLoader $robotLoader
 * @property Nette\Application\IRouter $router
 * @property Nette\Http\Session $session
 * @property Nette\Security\User $user
 * @property Tomaj\Image\ImageService $userImageService
 */
class SystemContainer extends Nette\DI\Container
{

	protected $meta = array(
		'types' => array(
			'nette\\object' => array(
				'nette',
				'nette.cacheJournal',
				'cacheStorage',
				'nette.httpRequestFactory',
				'httpRequest',
				'httpResponse',
				'nette.httpContext',
				'session',
				'nette.userStorage',
				'user',
				'application',
				'nette.presenterFactory',
				'nette.mailer',
				'nette.templateFactory',
				'nette.database.connection',
				'nette.database.connection.context',
				'doctrine.jitProxyWarmer',
				'facebook.config',
				'facebook.session',
				'facebook.client',
				'translation.catalogueCompiler',
				'translation.panel',
				'translation.userLocaleResolver.acceptHeader',
				'translation.userLocaleResolver.session',
				'translation.helpers',
				'translation.fallbackResolver',
				'translation.catalogueFactory',
				'translation.loadersInitializer',
				'translation.loader',
				'monolog.processor.priorityProcessor',
				'systemModule.applicationListener',
				'systemModule.messagesListener',
				'systemModule.paymentsListener',
				'systemModule.commentService',
				'systemModule.adminMenuControlFactory',
				'systemModule.categoriesMenuFactory',
				'systemModule.commonMenuControlFactory',
				'systemModule.protectedMenuControlFactory',
				'systemModule.publicMenuControlFactory',
				'systemModule.sportGroupService',
				'systemModule.sportTypeService',
				'systemModule.staticPageService',
				'systemModule.sportGroupListener',
				'systemModule.sportTypeListener',
				'systemModule.staticPageListener',
				'systemModule.userListener',
				'users.userService',
				'securityModule.aclRulesService',
				'securityModule.rolesService',
				'authenticator',
				'securityModule.aclService',
				'securityModule.positionsService',
				'securityModule.resourceService',
				'securityModule.aclRuleListener',
				'securityModule.applicationsListener',
				'seasons.adminPresenter',
				'seasons.seasonAppService',
				'seasons.seasonService',
				'seasons.seasonTaxService',
				'paymentsModule.paymentService',
				'paymentsModule.applicationsListener',
				'eventsModule.eventService',
				'articlesModule.rssPresenter',
				'articlesModule.articleService',
				'wallsModule.wallService',
				'communicationModule.forumService',
				'communicationModule.privateMessageService',
				'motivationModule.motivationEntryService',
				'motivationModule.motivationTaxService',
				'partnersModule.partnerService',
				'presenterTree',
				'robotLoader',
				'container',
			),
			'nette\\bridges\\framework\\netteaccessor' => array('nette'),
			'nette\\caching\\storages\\ijournal' => array('nette.cacheJournal'),
			'nette\\caching\\storages\\filejournal' => array('nette.cacheJournal'),
			'nette\\caching\\istorage' => array('cacheStorage'),
			'nette\\caching\\storages\\filestorage' => array('cacheStorage'),
			'nette\\http\\requestfactory' => array('nette.httpRequestFactory'),
			'nette\\http\\irequest' => array('httpRequest'),
			'nette\\http\\request' => array('httpRequest'),
			'nette\\http\\iresponse' => array('httpResponse'),
			'nette\\http\\response' => array('httpResponse'),
			'nette\\http\\context' => array('nette.httpContext'),
			'nette\\http\\session' => array('session'),
			'nette\\http\\userstorage' => array('nette.userStorage'),
			'nette\\security\\iuserstorage' => array('nette.userStorage'),
			'majkl578\\netteaddons\\doctrine2identity\\http\\userstorage' => array('nette.userStorage'),
			'nette\\security\\user' => array('user'),
			'nette\\application\\application' => array('application'),
			'nette\\application\\ipresenterfactory' => array('nette.presenterFactory'),
			'nette\\application\\presenterfactory' => array('nette.presenterFactory'),
			'nette\\application\\irouter' => array('router'),
			'nette\\mail\\imailer' => array('nette.mailer'),
			'nette\\mail\\sendmailmailer' => array('nette.mailer'),
			'nette\\bridges\\applicationlatte\\ilattefactory' => array('nette.latteFactory'),
			'nette\\application\\ui\\itemplatefactory' => array('nette.templateFactory'),
			'nette\\bridges\\applicationlatte\\templatefactory' => array('nette.templateFactory'),
			'nette\\database\\connection' => array('nette.database.connection'),
			'nette\\database\\context' => array('nette.database.connection.context'),
			'doctrine\\common\\annotations\\reader' => array('annotations.reader'),
			'kdyby\\events\\eventmanager' => array('events.manager'),
			'doctrine\\common\\eventmanager' => array('events.manager'),
			'kdyby\\events\\lazyeventmanager' => array('events.manager'),
			'doctrine\\orm\\entitymanager' => array('doctrine.default.entityManager'),
			'doctrine\\common\\persistence\\objectmanager' => array('doctrine.default.entityManager'),
			'doctrine\\orm\\entitymanagerinterface' => array('doctrine.default.entityManager'),
			'kdyby\\doctrine\\entitymanager' => array('doctrine.default.entityManager'),
			'doctrine\\dbal\\connection' => array('doctrine.default.connection'),
			'doctrine\\dbal\\driver\\connection' => array('doctrine.default.connection'),
			'kdyby\\doctrine\\connection' => array('doctrine.default.connection'),
			'doctrine\\orm\\entityrepository' => array('doctrine.dao'),
			'doctrine\\common\\collections\\selectable' => array('doctrine.dao'),
			'doctrine\\common\\persistence\\objectrepository' => array('doctrine.dao'),
			'kdyby\\persistence\\objectdao' => array('doctrine.dao'),
			'kdyby\\persistence\\queryexecutor' => array('doctrine.dao'),
			'kdyby\\persistence\\queryable' => array('doctrine.dao'),
			'kdyby\\doctrine\\entitydao' => array('doctrine.dao'),
			'kdyby\\doctrine\\entitydaofactory' => array('doctrine.daoFactory'),
			'doctrine\\orm\\tools\\schemavalidator' => array('doctrine.schemaValidator'),
			'doctrine\\orm\\tools\\schematool' => array('doctrine.schemaTool'),
			'doctrine\\dbal\\schema\\abstractschemamanager' => array('doctrine.schemaManager'),
			'kdyby\\doctrine\\proxy\\jitproxywarmer' => array('doctrine.jitProxyWarmer'),
			'symfony\\component\\console\\command\\command' => array(
				'doctrine.cli.0',
				'doctrine.cli.1',
				'doctrine.cli.2',
				'doctrine.cli.3',
				'doctrine.cli.4',
				'doctrine.cli.5',
				'doctrine.cli.6',
				'doctrine.cli.7',
				'doctrine.cli.8',
				'doctrine.cli.9',
				'translation.console.extract',
			),
			'doctrine\\dbal\\tools\\console\\command\\importcommand' => array('doctrine.cli.0'),
			'doctrine\\orm\\tools\\console\\command\\clearcache\\metadatacommand' => array('doctrine.cli.1'),
			'doctrine\\orm\\tools\\console\\command\\clearcache\\resultcommand' => array('doctrine.cli.2'),
			'doctrine\\orm\\tools\\console\\command\\clearcache\\querycommand' => array('doctrine.cli.3'),
			'doctrine\\orm\\tools\\console\\command\\schematool\\createcommand' => array('doctrine.cli.4'),
			'doctrine\\orm\\tools\\console\\command\\schematool\\abstractcommand' => array(
				'doctrine.cli.4',
				'doctrine.cli.5',
				'doctrine.cli.6',
			),
			'kdyby\\doctrine\\console\\schemacreatecommand' => array('doctrine.cli.4'),
			'doctrine\\orm\\tools\\console\\command\\schematool\\updatecommand' => array('doctrine.cli.5'),
			'kdyby\\doctrine\\console\\schemaupdatecommand' => array('doctrine.cli.5'),
			'doctrine\\orm\\tools\\console\\command\\schematool\\dropcommand' => array('doctrine.cli.6'),
			'kdyby\\doctrine\\console\\schemadropcommand' => array('doctrine.cli.6'),
			'doctrine\\orm\\tools\\console\\command\\generateproxiescommand' => array('doctrine.cli.7'),
			'kdyby\\doctrine\\console\\generateproxiescommand' => array('doctrine.cli.7'),
			'doctrine\\orm\\tools\\console\\command\\validateschemacommand' => array('doctrine.cli.8'),
			'kdyby\\doctrine\\console\\validateschemacommand' => array('doctrine.cli.8'),
			'doctrine\\orm\\tools\\console\\command\\infocommand' => array('doctrine.cli.9'),
			'kdyby\\doctrine\\console\\infocommand' => array('doctrine.cli.9'),
			'symfony\\component\\console\\helper\\helper' => array(
				'doctrine.helper.entityManager',
				'doctrine.helper.connection',
			),
			'symfony\\component\\console\\helper\\helperinterface' => array(
				'doctrine.helper.entityManager',
				'doctrine.helper.connection',
			),
			'doctrine\\orm\\tools\\console\\helper\\entitymanagerhelper' => array('doctrine.helper.entityManager'),
			'doctrine\\dbal\\tools\\console\\helper\\connectionhelper' => array('doctrine.helper.connection'),
			'kdyby\\facebook\\configuration' => array('facebook.config'),
			'kdyby\\facebook\\sessionstorage' => array('facebook.session'),
			'kdyby\\facebook\\apiclient' => array('facebook.apiClient'),
			'kdyby\\facebook\\facebook' => array('facebook.client'),
			'symfony\\component\\translation\\translator' => array('translation.default'),
			'symfony\\component\\translation\\translatorinterface' => array('translation.default'),
			'kdyby\\translation\\itranslator' => array('translation.default'),
			'nette\\localization\\itranslator' => array('translation.default'),
			'kdyby\\translation\\translator' => array('translation.default'),
			'kdyby\\translation\\cataloguecompiler' => array('translation.catalogueCompiler'),
			'tracy\\ibarpanel' => array('translation.panel'),
			'kdyby\\translation\\diagnostics\\panel' => array('translation.panel'),
			'kdyby\\translation\\iuserlocaleresolver' => array(
				'translation.userLocaleResolver.acceptHeader',
				'translation.userLocaleResolver.session',
				'translation.userLocaleResolver',
			),
			'kdyby\\translation\\localeresolver\\acceptheaderresolver' => array(
				'translation.userLocaleResolver.acceptHeader',
			),
			'kdyby\\translation\\localeresolver\\sessionresolver' => array(
				'translation.userLocaleResolver.session',
			),
			'kdyby\\translation\\templatehelpers' => array('translation.helpers'),
			'kdyby\\translation\\fallbackresolver' => array('translation.fallbackResolver'),
			'kdyby\\translation\\cataloguefactory' => array('translation.catalogueFactory'),
			'symfony\\component\\translation\\messageselector' => array('translation.selector'),
			'kdyby\\translation\\loadersinitializer' => array('translation.loadersInitializer'),
			'symfony\\component\\translation\\extractor\\extractorinterface' => array('translation.extractor'),
			'symfony\\component\\translation\\extractor\\chainextractor' => array('translation.extractor'),
			'symfony\\component\\translation\\writer\\translationwriter' => array('translation.writer'),
			'kdyby\\translation\\translationloader' => array('translation.loader'),
			'kdyby\\translation\\console\\extractcommand' => array('translation.console.extract'),
			'monolog\\logger' => array('monolog.logger'),
			'psr\\log\\loggerinterface' => array('monolog.logger'),
			'kdyby\\monolog\\logger' => array('monolog.logger'),
			'kdyby\\monolog\\processor\\priorityprocessor' => array('monolog.processor.priorityProcessor'),
			'monolog\\handler\\abstractsysloghandler' => array('monolog.handler.0'),
			'monolog\\handler\\abstractprocessinghandler' => array('monolog.handler.0'),
			'monolog\\handler\\abstracthandler' => array('monolog.handler.0'),
			'monolog\\handler\\handlerinterface' => array('monolog.handler.0'),
			'monolog\\handler\\sysloghandler' => array('monolog.handler.0'),
			'tracy\\logger' => array('monolog.adapter'),
			'kdyby\\monolog\\diagnostics\\monologadapter' => array('monolog.adapter'),
			'app\\systemmodule\\model\\service\\inotificationservice' => array('systemModule.notificationService'),
			'app\\systemmodule\\model\\service\\emailnotificationservice' => array('systemModule.notificationService'),
			'app\\systemmodule\\config\\initializer' => array('systemModule.initializer'),
			'kdyby\\events\\subscriber' => array(
				'systemModule.applicationListener',
				'systemModule.messagesListener',
				'systemModule.paymentsListener',
				'systemModule.sportGroupListener',
				'systemModule.sportTypeListener',
				'systemModule.staticPageListener',
				'systemModule.userListener',
				'securityModule.aclRuleListener',
				'securityModule.applicationsListener',
				'paymentsModule.applicationsListener',
			),
			'doctrine\\common\\eventsubscriber' => array(
				'systemModule.applicationListener',
				'systemModule.messagesListener',
				'systemModule.paymentsListener',
				'systemModule.sportGroupListener',
				'systemModule.sportTypeListener',
				'systemModule.staticPageListener',
				'systemModule.userListener',
				'securityModule.aclRuleListener',
				'securityModule.applicationsListener',
				'paymentsModule.applicationsListener',
			),
			'app\\systemmodule\\model\\listeners\\applicationslistener' => array('systemModule.applicationListener'),
			'app\\systemmodule\\model\\listeners\\messageslistener' => array('systemModule.messagesListener'),
			'app\\systemmodule\\model\\listeners\\paymentslistener' => array('systemModule.paymentsListener'),
			'app\\model\\service\\baseservice' => array(
				'systemModule.commentService',
				'systemModule.adminMenuControlFactory',
				'systemModule.categoriesMenuFactory',
				'systemModule.commonMenuControlFactory',
				'systemModule.protectedMenuControlFactory',
				'systemModule.publicMenuControlFactory',
				'systemModule.sportGroupService',
				'systemModule.sportTypeService',
				'systemModule.staticPageService',
				'users.userService',
				'securityModule.aclRulesService',
				'securityModule.rolesService',
				'securityModule.aclService',
				'securityModule.positionsService',
				'securityModule.resourceService',
				'seasons.seasonAppService',
				'seasons.seasonService',
				'seasons.seasonTaxService',
				'paymentsModule.paymentService',
				'eventsModule.eventService',
				'articlesModule.articleService',
				'wallsModule.wallService',
				'communicationModule.forumService',
				'communicationModule.privateMessageService',
				'motivationModule.motivationEntryService',
				'motivationModule.motivationTaxService',
				'partnersModule.partnerService',
			),
			'app\\systemmodule\\model\\service\\icommentservice' => array('systemModule.commentService'),
			'app\\systemmodule\\model\\service\\commentservice' => array('systemModule.commentService'),
			'app\\systemmodule\\model\\service\\menu\\iadminmenucontrolfactory' => array('systemModule.adminMenuControlFactory'),
			'app\\systemmodule\\model\\service\\menu\\adminmenucontrolfactory' => array('systemModule.adminMenuControlFactory'),
			'app\\systemmodule\\model\\service\\menu\\categoriesmenufactory' => array('systemModule.categoriesMenuFactory'),
			'app\\systemmodule\\model\\service\\menu\\icommonmenucontrolfactory' => array('systemModule.commonMenuControlFactory'),
			'app\\systemmodule\\model\\service\\menu\\commonmenucontrolfactory' => array('systemModule.commonMenuControlFactory'),
			'app\\systemmodule\\model\\service\\menu\\iprotectedmenucontrolfactory' => array(
				'systemModule.protectedMenuControlFactory',
			),
			'app\\systemmodule\\model\\service\\menu\\protectedmenucontrolfactory' => array(
				'systemModule.protectedMenuControlFactory',
			),
			'app\\systemmodule\\model\\service\\menu\\ipublicmenucontrolfactory' => array('systemModule.publicMenuControlFactory'),
			'app\\systemmodule\\model\\service\\menu\\publicmenucontrolfactory' => array('systemModule.publicMenuControlFactory'),
			'app\\systemmodule\\model\\service\\isportgroupservice' => array('systemModule.sportGroupService'),
			'app\\systemmodule\\model\\service\\sportgroupservice' => array('systemModule.sportGroupService'),
			'app\\systemmodule\\model\\service\\isporttypeservice' => array('systemModule.sportTypeService'),
			'app\\systemmodule\\model\\service\\sporttypeservice' => array('systemModule.sportTypeService'),
			'app\\systemmodule\\model\\service\\istaticpageservice' => array('systemModule.staticPageService'),
			'app\\systemmodule\\model\\service\\staticpageservice' => array('systemModule.staticPageService'),
			'app\\systemmodule\\model\\listeners\\sportgrouplistener' => array('systemModule.sportGroupListener'),
			'app\\systemmodule\\model\\listeners\\sporttypelistener' => array('systemModule.sportTypeListener'),
			'app\\systemmodule\\model\\listeners\\staticpagelistener' => array('systemModule.staticPageListener'),
			'app\\systemmodule\\model\\listeners\\userslistener' => array('systemModule.userListener'),
			'app\\usersmodule\\config\\initializer' => array('usersModule.initializer'),
			'app\\usersmodule\\model\\service\\iuserservice' => array('users.userService'),
			'app\\usersmodule\\model\\service\\userservice' => array('users.userService'),
			'app\\model\\service\\iaclruleservice' => array('securityModule.aclRulesService'),
			'app\\model\\service\\aclruleservice' => array('securityModule.aclRulesService'),
			'app\\model\\service\\iroleservice' => array('securityModule.rolesService'),
			'app\\model\\service\\roleservice' => array('securityModule.rolesService'),
			'app\\securitymodule\\config\\initializer' => array('securityModule.initializer'),
			'nette\\security\\iauthenticator' => array('authenticator'),
			'app\\securitymodule\\model\\authenticator' => array('authenticator'),
			'app\\securitymodule\\model\\service\\iaclservice' => array('securityModule.aclService'),
			'nette\\security\\iauthorizator' => array('securityModule.aclService'),
			'app\\securitymodule\\model\\service\\aclservice' => array('securityModule.aclService'),
			'app\\securitymodule\\model\\service\\ipositionservice' => array('securityModule.positionsService'),
			'app\\securitymodule\\model\\service\\positionservice' => array('securityModule.positionsService'),
			'app\\securitymodule\\model\\service\\iresourceservice' => array('securityModule.resourceService'),
			'app\\securitymodule\\model\\service\\resourceservice' => array('securityModule.resourceService'),
			'app\\securitymodule\\model\\listeners\\aclrulelistener' => array('securityModule.aclRuleListener'),
			'app\\securitymodule\\model\\listeners\\applicationslistener' => array('securityModule.applicationsListener'),
			'app\\systemmodule\\presenters\\systemadminpresenter' => array('seasons.adminPresenter'),
			'app\\systemmodule\\presenters\\securedpresenter' => array('seasons.adminPresenter'),
			'app\\systemmodule\\presenters\\basepresenter' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ui\\presenter' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ui\\control' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ui\\presentercomponent' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\componentmodel\\container' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\componentmodel\\component' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ui\\irenderable' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\componentmodel\\icontainer' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\componentmodel\\icomponent' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ui\\isignalreceiver' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ui\\istatepersistent' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'arrayaccess' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'nette\\application\\ipresenter' => array(
				'seasons.adminPresenter',
				'articlesModule.rssPresenter',
			),
			'app\\seasonsmodule\\presenters\\adminpresenter' => array('seasons.adminPresenter'),
			'app\\seasonsmodule\\model\\service\\iseasonapplicationservice' => array('seasons.seasonAppService'),
			'app\\seasonsmodule\\model\\service\\seasonapplicationservice' => array('seasons.seasonAppService'),
			'app\\seasonsmodule\\model\\service\\iseasonservice' => array('seasons.seasonService'),
			'app\\seasonsmodule\\model\\service\\seasonservice' => array('seasons.seasonService'),
			'app\\seasonsmodule\\model\\service\\iseasontaxservice' => array('seasons.seasonTaxService'),
			'app\\seasonsmodule\\model\\service\\seasontaxservice' => array('seasons.seasonTaxService'),
			'app\\paymentsmodule\\model\\service\\ipaymentservice' => array('paymentsModule.paymentService'),
			'app\\paymentsmodule\\model\\service\\paymentservice' => array('paymentsModule.paymentService'),
			'app\\paymentsmodule\\model\\listeners\\applicationslistener' => array('paymentsModule.applicationsListener'),
			'app\\eventsmodule\\model\\service\\ieventservice' => array('eventsModule.eventService'),
			'eventcalendar\\ieventmodel' => array('eventsModule.eventService'),
			'app\\eventsmodule\\model\\service\\eventservice' => array('eventsModule.eventService'),
			'app\\systemmodule\\presenters\\systempublicpresenter' => array('articlesModule.rssPresenter'),
			'app\\articlesmodule\\presenters\\rsspresenter' => array('articlesModule.rssPresenter'),
			'app\\articlesmodule\\model\\service\\iarticleservice' => array('articlesModule.articleService'),
			'app\\articlesmodule\\components\\rsscontrol\\irssmodel' => array('articlesModule.articleService'),
			'app\\articlesmodule\\model\\service\\articleservice' => array('articlesModule.articleService'),
			'app\\wallsmodule\\model\\service\\iwallservice' => array('wallsModule.wallService'),
			'app\\systemmodule\\model\\service\\icommenting' => array('wallsModule.wallService'),
			'app\\wallsmodule\\model\\service\\wallservice' => array('wallsModule.wallService'),
			'app\\communicationmodule\\model\\service\\iforumservice' => array('communicationModule.forumService'),
			'app\\communicationmodule\\model\\service\\forumservice' => array('communicationModule.forumService'),
			'app\\communicationmodule\\model\\service\\iprivatemessageservice' => array(
				'communicationModule.privateMessageService',
			),
			'app\\communicationmodule\\model\\service\\privatemessageservice' => array(
				'communicationModule.privateMessageService',
			),
			'app\\motivationmodule\\model\\service\\imotivationentryservice' => array(
				'motivationModule.motivationEntryService',
			),
			'app\\motivationmodule\\model\\service\\motivationentryservice' => array(
				'motivationModule.motivationEntryService',
			),
			'app\\motivationmodule\\model\\service\\imotivationtaxservice' => array('motivationModule.motivationTaxService'),
			'app\\motivationmodule\\model\\service\\motivationtaxservice' => array('motivationModule.motivationTaxService'),
			'app\\partnersmodule\\model\\service\\ipartnerservice' => array('partnersModule.partnerService'),
			'app\\partnersmodule\\model\\service\\partnerservice' => array('partnersModule.partnerService'),
			'app\\routerfactory' => array('159_App_RouterFactory'),
			'kdyby\\presentertree' => array('presenterTree'),
			'tomaj\\image\\backend\\backendinterface' => array('imagesBackend'),
			'tomaj\\image\\backend\\filebackend' => array('imagesBackend'),
			'nette\\loaders\\robotloader' => array('robotLoader'),
			'nette\\di\\container' => array('container'),
		),
		'tags' => array(
			'kdyby.subscriber' => array(
				'default.events.mysqlSessionInit' => TRUE,
				'paymentsModule.applicationsListener' => TRUE,
				'securityModule.aclRuleListener' => TRUE,
				'securityModule.applicationsListener' => TRUE,
				'systemModule.applicationListener' => TRUE,
				'systemModule.messagesListener' => TRUE,
				'systemModule.paymentsListener' => TRUE,
				'systemModule.sportGroupListener' => TRUE,
				'systemModule.sportTypeListener' => TRUE,
				'systemModule.staticPageListener' => TRUE,
				'systemModule.userListener' => TRUE,
			),
			'kdyby.console.command' => array(
				'doctrine.cli.0' => TRUE,
				'doctrine.cli.1' => TRUE,
				'doctrine.cli.2' => TRUE,
				'doctrine.cli.3' => TRUE,
				'doctrine.cli.4' => TRUE,
				'doctrine.cli.5' => TRUE,
				'doctrine.cli.6' => TRUE,
				'doctrine.cli.7' => TRUE,
				'doctrine.cli.8' => TRUE,
				'doctrine.cli.9' => TRUE,
				'translation.console.extract' => 'latte',
			),
			'kdyby.doctrine.connection' => array('doctrine.default.connection' => TRUE),
			'kdyby.doctrine.entityManager' => array(
				'doctrine.default.entityManager' => TRUE,
			),
			'kdyby.console.helper' => array(
				'doctrine.helper.connection' => 'db',
				'doctrine.helper.entityManager' => 'em',
			),
			'logger' => array('monolog.adapter' => TRUE),
			'monolog.handler' => array('monolog.handler.0' => TRUE),
			'monolog.processor' => array(
				'monolog.processor.priorityProcessor' => TRUE,
			),
			'run' => array(
				'securityModule.initializer' => TRUE,
				'systemModule.initializer' => TRUE,
				'usersModule.initializer' => TRUE,
			),
			'translation.dumper' => array(
				'translation.dumper.csv' => 'csv',
				'translation.dumper.ini' => 'ini',
				'translation.dumper.mo' => 'mo',
				'translation.dumper.neon' => 'neon',
				'translation.dumper.php' => 'php',
				'translation.dumper.po' => 'po',
				'translation.dumper.qt' => 'qt',
				'translation.dumper.res' => 'res',
				'translation.dumper.xliff' => 'xliff',
				'translation.dumper.yml' => 'yml',
			),
			'translation.extractor' => array(
				'translation.extractor.latte' => 'latte',
			),
			'translation.loader' => array(
				'translation.loader.csv' => 'csv',
				'translation.loader.dat' => 'dat',
				'translation.loader.ini' => 'ini',
				'translation.loader.mo' => 'mo',
				'translation.loader.neon' => 'neon',
				'translation.loader.php' => 'php',
				'translation.loader.po' => 'po',
				'translation.loader.res' => 'res',
				'translation.loader.ts' => 'ts',
				'translation.loader.xlf' => 'xlf',
				'translation.loader.yml' => 'yml',
			),
		),
	);


	public function __construct()
	{
		parent::__construct(array(
			'appDir' => '/home/fuca/Projects/www/sportsclub/tests',
			'wwwDir' => '/home/fuca/Projects/www/sportsclub/tests/../www/',
			'debugMode' => FALSE,
			'productionMode' => TRUE,
			'environment' => 'production',
			'consoleMode' => FALSE,
			'container' => array(
				'class' => 'SystemContainer',
				'parent' => 'Nette\\DI\\Container',
				'accessors' => TRUE,
			),
			'tempDir' => '/home/fuca/Projects/www/sportsclub/tests/tmp',
			'database' => array(
				'dsn' => 'mysql:host=127.0.0.1;dbname=sportsclub',
				'user' => 'root',
				'password' => 'pw',
				'options' => array('lazy' => TRUE),
			),
			'models' => array('salt' => '$2a06$05IKqFG8iuPts/cr0.'),
			'debugger' => array(
				'email' => 'michal.fuca.fucik@gmail.com',
			),
			'cacheDir' => '/home/fuca/Projects/www/sportsclub/tests/tmp/cache/',
			'modulesDir' => '/home/fuca/Projects/www/sportsclub/tests/modules/',
			'libsDir' => '/home/fuca/Projects/www/sportsclub/tests/../vendor/others',
			'imagesDir' => '/home/fuca/Projects/www/sportsclub/tests/../www//assets/images/',
			'appName' => 'sportsclub',
			'appDefaultEmail' => 'michal.fuca.fucik@gmail.com',
			'doctrine.debug' => FALSE,
			'doctrine.orm.defaultEntityManager' => 'default',
			'logDir' => '/home/fuca/Projects/www/sportsclub/tests/log',
		));
	}


	/**
	 * @return App\RouterFactory
	 */
	public function createService__159_App_RouterFactory()
	{
		$service = new App\RouterFactory;
		return $service;
	}


	/**
	 * @return Doctrine\Common\Cache\Cache
	 */
	public function createServiceAnnotations__cache__annotations()
	{
		$service = new Kdyby\DoctrineCache\Cache($this->getService('cacheStorage'), 'Doctrine.Annotations', FALSE);
		if (!$service instanceof Doctrine\Common\Cache\Cache) {
			throw new Nette\UnexpectedValueException('Unable to create service \'annotations.cache.annotations\', value returned by factory is not Doctrine\\Common\\Cache\\Cache type.');
		}
		$service->setNamespace('Kdyby_annotations.cache.annotations_16ad1128');
		return $service;
	}


	/**
	 * @return Doctrine\Common\Annotations\Reader
	 */
	public function createServiceAnnotations__reader()
	{
		$service = new Doctrine\Common\Annotations\CachedReader($this->getService('annotations.reflectionReader'), $this->getService('annotations.cache.annotations'), TRUE);
		if (!$service instanceof Doctrine\Common\Annotations\Reader) {
			throw new Nette\UnexpectedValueException('Unable to create service \'annotations.reader\', value returned by factory is not Doctrine\\Common\\Annotations\\Reader type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\Common\Annotations\AnnotationReader
	 */
	public function createServiceAnnotations__reflectionReader()
	{
		$service = new Doctrine\Common\Annotations\AnnotationReader;
		$service->addGlobalIgnoredName('persistent');
		$service->addGlobalIgnoredName('serializationVersion');
		$service->addGlobalIgnoredName('hideInTree');
		return $service;
	}


	/**
	 * @return Nette\Application\Application
	 */
	public function createServiceApplication()
	{
		$service = new Nette\Application\Application($this->getService('nette.presenterFactory'), $this->getService('router'), $this->getService('httpRequest'), $this->getService('httpResponse'));
		$service->catchExceptions = FALSE;
		$service->errorPresenter = 'Public:Error';
		Nette\Bridges\ApplicationTracy\RoutingPanel::initializePanel($service);
		$service->onRequest[] = array(
			$this->getService('translation.userLocaleResolver.param'),
			'onRequest',
		);
		$self = $this; $service->onStartup[] = function () use ($self) { $self->getService('translation.default'); };
		$service->onRequest[] = array(
			$this->getService('translation.panel'),
			'onRequest',
		);
		$service->onStartup = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\Application',
			'onStartup',
		), $service->onStartup);
		$service->onShutdown = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\Application',
			'onShutdown',
		), $service->onShutdown);
		$service->onRequest = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\Application',
			'onRequest',
		), $service->onRequest);
		$service->onPresenter = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\Application',
			'onPresenter',
		), $service->onPresenter);
		$service->onResponse = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\Application',
			'onResponse',
		), $service->onResponse);
		$service->onError = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\Application',
			'onError',
		), $service->onError);
		return $service;
	}


	/**
	 * @return Tomaj\Image\ImageService
	 */
	public function createServiceArticleImageService()
	{
		$service = new \Tomaj\Image\ImageService($this->getService('imagesBackend'), 'articles/:year/:month/:hash', array('340x200', '700x400', '1028x370'), 70);
		if (!$service instanceof Tomaj\Image\ImageService) {
			throw new Nette\UnexpectedValueException('Unable to create service \'articleImageService\', value returned by factory is not Tomaj\\Image\\ImageService type.');
		}
		return $service;
	}


	/**
	 * @return App\ArticlesModule\Model\Service\ArticleService
	 */
	public function createServiceArticlesModule__articleService()
	{
		$service = new App\ArticlesModule\Model\Service\ArticleService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('articlesModule.cacheStorage'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setUserService($this->getService('users.userService'));
		$service->setImageService($this->getService('articleImageService'));
		$service->setConfig(array(
			'defaultImagePath' => 'article',
			'defafaultThumbnail' => 'articleThumbDefault.png',
			'defaultImage' => 'articleImageDefault.png',
			'defaultRssLimit' => 50,
			'rss' => array(
				'title' => 'FBC Mohelnice',
				'description' => 'Webová prezentace florbalového klubu FBC Mohelnice, o.s.',
				'category' => 'aktuality,články,novinky,RSS',
				'copyright' => 'FBC Mohelnice',
				'managingEditor' => 'editor@fbcmohelnice.cz',
				'webmaster' => 'webmaster@fbcmohelnice.cz',
			),
		));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\ArticlesModule\\Model\\Service\\ArticleService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\ArticlesModule\\Model\\Service\\ArticleService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\ArticlesModule\\Model\\Service\\ArticleService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceArticlesModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/articlesModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\ArticlesModule\Presenters\RssPresenter
	 */
	public function createServiceArticlesModule__rssPresenter()
	{
		$service = new App\ArticlesModule\Presenters\RssPresenter;
		$service->setRssPropertiesConfig(array(
			'title' => 'FBC Mohelnice',
			'description' => 'Webová prezentace florbalového klubu FBC Mohelnice, o.s.',
			'category' => 'aktuality,články,novinky,RSS',
			'copyright' => 'FBC Mohelnice',
			'managingEditor' => 'editor@fbcmohelnice.cz',
			'webmaster' => 'webmaster@fbcmohelnice.cz',
		));
		$service->onShutdown = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\UI\\Presenter',
			'onShutdown',
		), $service->onShutdown);
		return $service;
	}


	/**
	 * @return App\SecurityModule\Model\Authenticator
	 */
	public function createServiceAuthenticator()
	{
		$service = new App\SecurityModule\Model\Authenticator;
		$service->setSalt('$2a06$05IKqFG8iuPts/cr0.');
		$service->setRolesService($this->getService('securityModule.rolesService'));
		$service->setUsersService($this->getService('users.userService'));
		$service->setLogger($this->getService('monolog.logger'));
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceCacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceCommunicationModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/communicationModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\CommunicationModule\Model\Service\ForumService
	 */
	public function createServiceCommunicationModule__forumService()
	{
		$service = new App\CommunicationModule\Model\Service\ForumService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('communicationModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setCommentService($this->getService('systemModule.commentService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\CommunicationModule\\Model\\Service\\ForumService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\CommunicationModule\\Model\\Service\\ForumService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\CommunicationModule\\Model\\Service\\ForumService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\CommunicationModule\Model\Service\PrivateMessageService
	 */
	public function createServiceCommunicationModule__privateMessageService()
	{
		$service = new App\CommunicationModule\Model\Service\PrivateMessageService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('communicationModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\CommunicationModule\\Model\\Service\\PrivateMessageService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\CommunicationModule\\Model\\Service\\PrivateMessageService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\CommunicationModule\\Model\\Service\\PrivateMessageService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Nette\DI\Container
	 */
	public function createServiceContainer()
	{
		return $this;
	}


	/**
	 * @return Doctrine\DBAL\Event\Listeners\MysqlSessionInit
	 */
	public function createServiceDefault__events__mysqlSessionInit()
	{
		$service = new Doctrine\DBAL\Event\Listeners\MysqlSessionInit('UTF8');
		return $service;
	}


	/**
	 * @return Doctrine\Common\Cache\Cache
	 */
	public function createServiceDoctrine__cache__default__dbalResult()
	{
		$service = new Kdyby\DoctrineCache\Cache($this->getService('cacheStorage'), 'Doctrine.default.dbalResult', FALSE);
		if (!$service instanceof Doctrine\Common\Cache\Cache) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.cache.default.dbalResult\', value returned by factory is not Doctrine\\Common\\Cache\\Cache type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\Common\Cache\Cache
	 */
	public function createServiceDoctrine__cache__default__hydration()
	{
		$service = new Kdyby\DoctrineCache\Cache($this->getService('cacheStorage'), 'Doctrine.default.hydration', FALSE);
		if (!$service instanceof Doctrine\Common\Cache\Cache) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.cache.default.hydration\', value returned by factory is not Doctrine\\Common\\Cache\\Cache type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\Common\Cache\Cache
	 */
	public function createServiceDoctrine__cache__default__metadata()
	{
		$service = new Kdyby\DoctrineCache\Cache($this->getService('cacheStorage'), 'Doctrine.default.metadata', FALSE);
		if (!$service instanceof Doctrine\Common\Cache\Cache) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.cache.default.metadata\', value returned by factory is not Doctrine\\Common\\Cache\\Cache type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\Common\Cache\Cache
	 */
	public function createServiceDoctrine__cache__default__ormResult()
	{
		$service = new Kdyby\DoctrineCache\Cache($this->getService('cacheStorage'), 'Doctrine.default.ormResult', FALSE);
		if (!$service instanceof Doctrine\Common\Cache\Cache) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.cache.default.ormResult\', value returned by factory is not Doctrine\\Common\\Cache\\Cache type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\Common\Cache\Cache
	 */
	public function createServiceDoctrine__cache__default__query()
	{
		$service = new Kdyby\DoctrineCache\Cache($this->getService('cacheStorage'), 'Doctrine.default.query', FALSE);
		if (!$service instanceof Doctrine\Common\Cache\Cache) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.cache.default.query\', value returned by factory is not Doctrine\\Common\\Cache\\Cache type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\DBAL\Tools\Console\Command\ImportCommand
	 */
	public function createServiceDoctrine__cli__0()
	{
		$service = new Doctrine\DBAL\Tools\Console\Command\ImportCommand;
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand
	 */
	public function createServiceDoctrine__cli__1()
	{
		$service = new Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand
	 */
	public function createServiceDoctrine__cli__2()
	{
		$service = new Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand
	 */
	public function createServiceDoctrine__cli__3()
	{
		$service = new Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Console\SchemaCreateCommand
	 */
	public function createServiceDoctrine__cli__4()
	{
		$service = new Kdyby\Doctrine\Console\SchemaCreateCommand($this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Console\SchemaUpdateCommand
	 */
	public function createServiceDoctrine__cli__5()
	{
		$service = new Kdyby\Doctrine\Console\SchemaUpdateCommand($this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Console\SchemaDropCommand
	 */
	public function createServiceDoctrine__cli__6()
	{
		$service = new Kdyby\Doctrine\Console\SchemaDropCommand($this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Console\GenerateProxiesCommand
	 */
	public function createServiceDoctrine__cli__7()
	{
		$service = new Kdyby\Doctrine\Console\GenerateProxiesCommand($this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Console\ValidateSchemaCommand
	 */
	public function createServiceDoctrine__cli__8()
	{
		$service = new Kdyby\Doctrine\Console\ValidateSchemaCommand($this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Console\InfoCommand
	 */
	public function createServiceDoctrine__cli__9()
	{
		$service = new Kdyby\Doctrine\Console\InfoCommand($this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\EntityDao
	 */
	public function createServiceDoctrine__dao($entityName)
	{
		$service = $this->getService('doctrine.default.entityManager')->getDao($entityName);
		if (!$service instanceof Kdyby\Doctrine\EntityDao) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.dao\', value returned by factory is not Kdyby\\Doctrine\\EntityDao type.');
		}
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\EntityDaoFactory
	 */
	public function createServiceDoctrine__daoFactory()
	{
		return new SystemContainer_Kdyby_Doctrine_EntityDaoFactoryImpl_doctrine_daoFactory($this);
	}


	/**
	 * @return Kdyby\Doctrine\Connection
	 */
	public function createServiceDoctrine__default__connection()
	{
		$service = Kdyby\Doctrine\Connection::create(array(
			'dbname' => 'tests',
			'host' => '127.0.0.1',
			'port' => NULL,
			'user' => 'root',
			'password' => '880609Fuca',
			'charset' => 'UTF8',
			'driver' => 'pdo_mysql',
			'driverClass' => NULL,
			'options' => NULL,
			'path' => NULL,
			'memory' => NULL,
			'unix_socket' => NULL,
			'platformService' => NULL,
			'defaultTableOptions' => array(),
			'debug' => FALSE,
		), $this->getService('doctrine.default.dbalConfiguration'), $this->getService('events.manager'), array(
			'enum' => 'Kdyby\\Doctrine\\Types\\Enum',
			'point' => 'Kdyby\\Doctrine\\Types\\Point',
			'lineString' => 'Kdyby\\Doctrine\\Types\\LineString',
			'multiLineString' => 'Kdyby\\Doctrine\\Types\\MultiLineString',
			'polygon' => 'Kdyby\\Doctrine\\Types\\Polygon',
			'multiPolygon' => 'Kdyby\\Doctrine\\Types\\MultiPolygon',
			'geometryCollection' => 'Kdyby\\Doctrine\\Types\\GeometryCollection',
			'MotivationEntryType' => 'App\\Model\\Misc\\Enum\\MotivationEntryType',
		), array(
			'enum' => 'enum',
			'point' => 'point',
			'lineString' => 'lineString',
			'multiLineString' => 'multiLineString',
			'polygon' => 'polygon',
			'multiPolygon' => 'multiPolygon',
			'geometryCollection' => 'geometryCollection',
			'MotivationEntryType' => 'MotivationEntryType',
		));
		if (!$service instanceof Kdyby\Doctrine\Connection) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.default.connection\', value returned by factory is not Kdyby\\Doctrine\\Connection type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\DBAL\Configuration
	 */
	public function createServiceDoctrine__default__dbalConfiguration()
	{
		$service = new Doctrine\DBAL\Configuration;
		$service->setResultCacheImpl($this->getService('doctrine.cache.default.dbalResult'));
		$service->setSQLLogger(new Doctrine\DBAL\Logging\LoggerChain);
		return $service;
	}


	/**
	 * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
	 */
	public function createServiceDoctrine__default__driver__App__annotationsImpl()
	{
		$service = new Kdyby\Doctrine\Mapping\AnnotationDriver(array(
			'/home/fuca/Projects/www/sportsclub/tests',
		), $this->getService('annotations.reader'));
		if (!$service instanceof Doctrine\Common\Persistence\Mapping\Driver\MappingDriver) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.default.driver.App.annotationsImpl\', value returned by factory is not Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriver type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
	 */
	public function createServiceDoctrine__default__driver__Kdyby_Doctrine__annotationsImpl()
	{
		$service = new Kdyby\Doctrine\Mapping\AnnotationDriver(array(
			'/home/fuca/Projects/www/sportsclub/vendor/kdyby/doctrine/src/Kdyby/Doctrine/DI/../Entities',
		), $this->getService('annotations.reader'));
		if (!$service instanceof Doctrine\Common\Persistence\Mapping\Driver\MappingDriver) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.default.driver.Kdyby_Doctrine.annotationsImpl\', value returned by factory is not Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriver type.');
		}
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\EntityManager
	 */
	public function createServiceDoctrine__default__entityManager()
	{
		$service = Kdyby\Doctrine\EntityManager::create($this->getService('doctrine.default.connection'), $this->getService('doctrine.default.ormConfiguration'), $this->getService('events.manager'));
		if (!$service instanceof Kdyby\Doctrine\EntityManager) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.default.entityManager\', value returned by factory is not Kdyby\\Doctrine\\EntityManager type.');
		}
		$service->onDaoCreate = $this->getService('events.manager')->createEvent(array(
			'Kdyby\\Doctrine\\EntityManager',
			'onDaoCreate',
		), $service->onDaoCreate);
		return $service;
	}


	/**
	 * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain
	 */
	public function createServiceDoctrine__default__metadataDriver()
	{
		$service = new Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
		$service->addDriver($this->getService('doctrine.default.driver.App.annotationsImpl'), 'App');
		$service->addDriver($this->getService('doctrine.default.driver.Kdyby_Doctrine.annotationsImpl'), 'Kdyby\\Doctrine');
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Configuration
	 */
	public function createServiceDoctrine__default__ormConfiguration()
	{
		$service = new Kdyby\Doctrine\Configuration;
		$service->setMetadataCacheImpl($this->getService('doctrine.cache.default.metadata'));
		$service->setQueryCacheImpl($this->getService('doctrine.cache.default.query'));
		$service->setResultCacheImpl($this->getService('doctrine.cache.default.ormResult'));
		$service->setHydrationCacheImpl($this->getService('doctrine.cache.default.hydration'));
		$service->setMetadataDriverImpl($this->getService('doctrine.default.metadataDriver'));
		$service->setClassMetadataFactoryName('Kdyby\\Doctrine\\Mapping\\ClassMetadataFactory');
		$service->setDefaultRepositoryClassName('Kdyby\\Doctrine\\EntityDao');
		$service->setProxyDir('/home/fuca/Projects/www/sportsclub/tests/tmp/proxies');
		$service->setProxyNamespace('Kdyby\\GeneratedProxy');
		$service->setAutoGenerateProxyClasses(FALSE);
		$service->setEntityNamespaces(array());
		$service->setCustomHydrationModes(array());
		$service->setCustomStringFunctions(array());
		$service->setCustomNumericFunctions(array());
		$service->setCustomDatetimeFunctions(array());
		$service->setNamingStrategy(new Doctrine\ORM\Mapping\DefaultNamingStrategy);
		$service->setQuoteStrategy(new Doctrine\ORM\Mapping\DefaultQuoteStrategy);
		$service->setEntityListenerResolver(new Kdyby\Doctrine\Mapping\EntityListenerResolver($this));
		return $service;
	}


	/**
	 * @return Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper
	 */
	public function createServiceDoctrine__helper__connection()
	{
		$service = new Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($this->getService('doctrine.default.connection'));
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper
	 */
	public function createServiceDoctrine__helper__entityManager()
	{
		$service = new Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($this->getService('doctrine.default.entityManager'));
		return $service;
	}


	/**
	 * @return Kdyby\Doctrine\Proxy\JitProxyWarmer
	 */
	public function createServiceDoctrine__jitProxyWarmer()
	{
		$service = new Kdyby\Doctrine\Proxy\JitProxyWarmer;
		return $service;
	}


	/**
	 * @return Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	public function createServiceDoctrine__schemaManager()
	{
		$service = $this->getService('doctrine.default.connection')->getSchemaManager();
		if (!$service instanceof Doctrine\DBAL\Schema\AbstractSchemaManager) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.schemaManager\', value returned by factory is not Doctrine\\DBAL\\Schema\\AbstractSchemaManager type.');
		}
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Tools\SchemaTool
	 */
	public function createServiceDoctrine__schemaTool()
	{
		$service = new Doctrine\ORM\Tools\SchemaTool($this->getService('doctrine.default.entityManager'));
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Tools\SchemaValidator
	 */
	public function createServiceDoctrine__schemaValidator()
	{
		$service = new Doctrine\ORM\Tools\SchemaValidator($this->getService('doctrine.default.entityManager'));
		return $service;
	}


	/**
	 * @return Kdyby\Events\LazyEventManager
	 */
	public function createServiceEvents__manager()
	{
		$service = new Kdyby\Events\LazyEventManager(array(
			'postConnect' => array('default.events.mysqlSessionInit'),
			'App\\SeasonsModule\\Model\\Service\\SeasonApplicationService::onCreate' => array(
				'systemModule.applicationListener',
				'securityModule.applicationsListener',
				'paymentsModule.applicationsListener',
			),
			'onCreate' => array(
				'systemModule.applicationListener',
				'systemModule.messagesListener',
				'systemModule.paymentsListener',
				'systemModule.sportGroupListener',
				'systemModule.sportTypeListener',
				'systemModule.staticPageListener',
				'systemModule.userListener',
				'securityModule.aclRuleListener',
				'securityModule.applicationsListener',
				'paymentsModule.applicationsListener',
			),
			'App\\CommunicationModule\\Model\\Service\\PrivateMessageService::onCreate' => array('systemModule.messagesListener'),
			'App\\PaymentsModule\\Model\\Service\\PaymentService::onCreate' => array('systemModule.paymentsListener'),
			'App\\SystemModule\\Model\\Service\\SportGroupService::onCreate' => array('systemModule.sportGroupListener'),
			'App\\SystemModule\\Model\\Service\\SportGroupService::onUpdate' => array('systemModule.sportGroupListener'),
			'onUpdate' => array(
				'systemModule.sportGroupListener',
				'systemModule.sportTypeListener',
				'systemModule.staticPageListener',
				'securityModule.aclRuleListener',
			),
			'App\\SystemModule\\Model\\Service\\SportGroupService::onDelete' => array('systemModule.sportGroupListener'),
			'onDelete' => array(
				'systemModule.sportGroupListener',
				'systemModule.sportTypeListener',
				'systemModule.staticPageListener',
				'securityModule.aclRuleListener',
			),
			'App\\SystemModule\\Model\\Service\\SportTypeService::onCreate' => array('systemModule.sportTypeListener'),
			'App\\SystemModule\\Model\\Service\\SportTypeService::onUpdate' => array('systemModule.sportTypeListener'),
			'App\\SystemModule\\Model\\Service\\SportTypeService::onDelete' => array('systemModule.sportTypeListener'),
			'App\\SystemModule\\Model\\Service\\StaticPageService::onCreate' => array('systemModule.staticPageListener'),
			'App\\SystemModule\\Model\\Service\\StaticPageService::onUpdate' => array('systemModule.staticPageListener'),
			'App\\SystemModule\\Model\\Service\\StaticPageService::onDelete' => array('systemModule.staticPageListener'),
			'App\\UsersModule\\Model\\Service\\UserService::onCreate' => array('systemModule.userListener'),
			'App\\UsersModule\\Model\\Service\\UserService::onActivate' => array('systemModule.userListener'),
			'onActivate' => array('systemModule.userListener'),
			'App\\UsersModule\\Model\\Service\\UserService::onDeactivate' => array('systemModule.userListener'),
			'onDeactivate' => array('systemModule.userListener'),
			'App\\UsersModule\\Model\\Service\\UserService::onPasswordRegenerate' => array('systemModule.userListener'),
			'onPasswordRegenerate' => array('systemModule.userListener'),
			'App\\Model\\Service\\AclRuleService::onCreate' => array('securityModule.aclRuleListener'),
			'App\\Model\\Service\\AclRuleService::onUpdate' => array('securityModule.aclRuleListener'),
			'App\\Model\\Service\\AclRuleService::onDelete' => array('securityModule.aclRuleListener'),
		), $this);
		Kdyby\Events\Diagnostics\Panel::register($service, $this)->renderPanel = TRUE;
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceEventsModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/eventsModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\EventsModule\Model\Service\EventService
	 */
	public function createServiceEventsModule__eventService()
	{
		$service = new App\EventsModule\Model\Service\EventService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('eventsModule.cacheStorage'));
		$service->setGroupService($this->getService('systemModule.sportGroupService'));
		$service->setUserService($this->getService('users.userService'));
		$service->setCommentService($this->getService('systemModule.commentService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\EventsModule\\Model\\Service\\EventService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\EventsModule\\Model\\Service\\EventService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\EventsModule\\Model\\Service\\EventService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Kdyby\Facebook\ApiClient
	 */
	public function createServiceFacebook__apiClient()
	{
		$service = new Kdyby\Facebook\Api\CurlClient;
		if (!$service instanceof Kdyby\Facebook\ApiClient) {
			throw new Nette\UnexpectedValueException('Unable to create service \'facebook.apiClient\', value returned by factory is not Kdyby\\Facebook\\ApiClient type.');
		}
		$service->curlOptions = array(
			'CURLOPT_CONNECTTIMEOUT' => 10,
			'CURLOPT_TIMEOUT' => 60,
			'CURLOPT_HTTPHEADER' => array('User-Agent: kdyby-facebook-1.1'),
			'CURLINFO_HEADER_OUT' => TRUE,
			'CURLOPT_HEADER' => TRUE,
			'CURLOPT_RETURNTRANSFER' => TRUE,
		);;
		$service->onRequest = $this->getService('events.manager')->createEvent(array(
			'Kdyby\\Facebook\\Api\\CurlClient',
			'onRequest',
		), $service->onRequest);
		$service->onError = $this->getService('events.manager')->createEvent(array(
			'Kdyby\\Facebook\\Api\\CurlClient',
			'onError',
		), $service->onError);
		$service->onSuccess = $this->getService('events.manager')->createEvent(array(
			'Kdyby\\Facebook\\Api\\CurlClient',
			'onSuccess',
		), $service->onSuccess);
		return $service;
	}


	/**
	 * @return Kdyby\Facebook\Facebook
	 */
	public function createServiceFacebook__client()
	{
		$service = new Kdyby\Facebook\Facebook($this->getService('facebook.config'), $this->getService('facebook.session'), $this->getService('facebook.apiClient'), $this->getService('httpRequest'), $this->getService('httpResponse'));
		return $service;
	}


	/**
	 * @return Kdyby\Facebook\Configuration
	 */
	public function createServiceFacebook__config()
	{
		$service = new Kdyby\Facebook\Configuration('1234567890', 'e807f1fcf82d132f9bb018ca6738a19f');
		$service->fileUploadSupport = FALSE;
		$service->trustForwarded = FALSE;
		$service->permissions = array('email');
		$service->canvasBaseUrl = NULL;
		$service->graphVersion = '';
		return $service;
	}


	/**
	 * @return Kdyby\Facebook\SessionStorage
	 */
	public function createServiceFacebook__session()
	{
		$service = new Kdyby\Facebook\SessionStorage($this->getService('session'), $this->getService('facebook.config'));
		return $service;
	}


	/**
	 * @return Nette\Http\Request
	 */
	public function createServiceHttpRequest()
	{
		$service = $this->getService('nette.httpRequestFactory')->createHttpRequest();
		if (!$service instanceof Nette\Http\Request) {
			throw new Nette\UnexpectedValueException('Unable to create service \'httpRequest\', value returned by factory is not Nette\\Http\\Request type.');
		}
		return $service;
	}


	/**
	 * @return Nette\Http\Response
	 */
	public function createServiceHttpResponse()
	{
		$service = new Nette\Http\Response;
		return $service;
	}


	/**
	 * @return Tomaj\Image\Backend\FileBackend
	 */
	public function createServiceImagesBackend()
	{
		$service = new \Tomaj\Image\Backend\FileBackend('/home/fuca/Projects/www/sportsclub/tests/../www/', '/home/fuca/Projects/www/sportsclub/tests/../www//assets/images/');
		if (!$service instanceof Tomaj\Image\Backend\FileBackend) {
			throw new Nette\UnexpectedValueException('Unable to create service \'imagesBackend\', value returned by factory is not Tomaj\\Image\\Backend\\FileBackend type.');
		}
		return $service;
	}


	/**
	 * @return Kdyby\Monolog\Diagnostics\MonologAdapter
	 */
	public function createServiceMonolog__adapter()
	{
		$service = new Kdyby\Monolog\Diagnostics\MonologAdapter($this->getService('monolog.logger'));
		return $service;
	}


	/**
	 * @return Monolog\Handler\SyslogHandler
	 */
	public function createServiceMonolog__handler__0()
	{
		$service = new Monolog\Handler\SyslogHandler('sportsclub', 'local4');
		return $service;
	}


	/**
	 * @return Kdyby\Monolog\Logger
	 */
	public function createServiceMonolog__logger()
	{
		$service = new Kdyby\Monolog\Logger('sportsclub');
		$service->pushHandler($this->getService('monolog.handler.0'));
		$service->pushProcessor($this->getService('monolog.processor.priorityProcessor'));
		$service->pushHandler(new Kdyby\Monolog\Handler\FallbackNetteHandler('sportsclub', '/home/fuca/Projects/www/sportsclub/tests/log'));
		return $service;
	}


	/**
	 * @return Kdyby\Monolog\Processor\PriorityProcessor
	 */
	public function createServiceMonolog__processor__priorityProcessor()
	{
		$service = new Kdyby\Monolog\Processor\PriorityProcessor;
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceMotivationModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/motivationModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\MotivationModule\Model\Service\MotivationEntryService
	 */
	public function createServiceMotivationModule__motivationEntryService()
	{
		$service = new App\MotivationModule\Model\Service\MotivationEntryService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('motivationModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->setSeasonService($this->getService('seasons.seasonService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\MotivationModule\\Model\\Service\\MotivationEntryService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\MotivationModule\\Model\\Service\\MotivationEntryService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\MotivationModule\\Model\\Service\\MotivationEntryService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\MotivationModule\Model\Service\MotivationTaxService
	 */
	public function createServiceMotivationModule__motivationTaxService()
	{
		$service = new App\MotivationModule\Model\Service\MotivationTaxService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('motivationModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->setSeasonService($this->getService('seasons.seasonService'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\MotivationModule\\Model\\Service\\MotivationTaxService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\MotivationModule\\Model\\Service\\MotivationTaxService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\MotivationModule\\Model\\Service\\MotivationTaxService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Nette\Bridges\Framework\NetteAccessor
	 */
	public function createServiceNette()
	{
		$service = new Nette\Bridges\Framework\NetteAccessor($this);
		return $service;
	}


	/**
	 * @return Nette\Caching\Cache
	 */
	public function createServiceNette__cache($namespace = NULL)
	{
		$service = new Nette\Caching\Cache($this->getService('cacheStorage'), $namespace);
		trigger_error('Service cache is deprecated.', 16384);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileJournal
	 */
	public function createServiceNette__cacheJournal()
	{
		$service = new Nette\Caching\Storages\FileJournal('/home/fuca/Projects/www/sportsclub/tests/tmp');
		return $service;
	}


	/**
	 * @return Nette\Database\Connection
	 */
	public function createServiceNette__database__connection()
	{
		$service = new Nette\Database\Connection('mysql:host=127.0.0.1;dbname=sportsclub', 'root', 'pw', array('lazy' => TRUE));
		Tracy\Debugger::getBlueScreen()->addPanel('Nette\\Bridges\\DatabaseTracy\\ConnectionPanel::renderException');
		$service->onConnect = $this->getService('events.manager')->createEvent(array(
			'Nette\\Database\\Connection',
			'onConnect',
		), $service->onConnect);
		$service->onQuery = $this->getService('events.manager')->createEvent(array(
			'Nette\\Database\\Connection',
			'onQuery',
		), $service->onQuery);
		return $service;
	}


	/**
	 * @return Nette\Database\Context
	 */
	public function createServiceNette__database__connection__context()
	{
		$service = new Nette\Database\Context($this->getService('nette.database.connection'), new Nette\Database\Reflection\DiscoveredReflection($this->getService('nette.database.connection'), $this->getService('cacheStorage')), $this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Nette\Http\Context
	 */
	public function createServiceNette__httpContext()
	{
		$service = new Nette\Http\Context($this->getService('httpRequest'), $this->getService('httpResponse'));
		return $service;
	}


	/**
	 * @return Nette\Http\RequestFactory
	 */
	public function createServiceNette__httpRequestFactory()
	{
		$service = new Nette\Http\RequestFactory;
		$service->setProxy(array());
		return $service;
	}


	/**
	 * @return Latte\Engine
	 */
	public function createServiceNette__latte()
	{
		$service = new Latte\Engine;
		$service->setTempDirectory('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/latte');
		$service->setAutoRefresh(FALSE);
		$service->setContentType('html');
		$service->onCompile = $this->getService('events.manager')->createEvent(array('Latte\\Engine', 'onCompile'), $service->onCompile);
		return $service;
	}


	/**
	 * @return Nette\Bridges\ApplicationLatte\ILatteFactory
	 */
	public function createServiceNette__latteFactory()
	{
		return new SystemContainer_Nette_Bridges_ApplicationLatte_ILatteFactoryImpl_nette_latteFactory($this);
	}


	/**
	 * @return Nette\Mail\SendmailMailer
	 */
	public function createServiceNette__mailer()
	{
		$service = new Nette\Mail\SendmailMailer;
		return $service;
	}


	/**
	 * @return Nette\Application\PresenterFactory
	 */
	public function createServiceNette__presenterFactory()
	{
		$service = new Nette\Application\PresenterFactory('/home/fuca/Projects/www/sportsclub/tests', $this);
		$service->setMapping(array(
			'*' => 'App\\*Module\\Presenters\\*Presenter',
		));
		return $service;
	}


	/**
	 * @return Nette\Templating\FileTemplate
	 */
	public function createServiceNette__template()
	{
		$service = new Nette\Templating\FileTemplate;
		$service->registerFilter($this->getService('nette.latteFactory')->create());
		$service->registerHelperLoader('Nette\\Templating\\Helpers::loader');
		$service->onPrepareFilters = $this->getService('events.manager')->createEvent(array(
			'Nette\\Templating\\Template',
			'onPrepareFilters',
		), $service->onPrepareFilters);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\PhpFileStorage
	 */
	public function createServiceNette__templateCacheStorage()
	{
		$service = new Nette\Caching\Storages\PhpFileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache', $this->getService('nette.cacheJournal'));
		trigger_error('Service templateCacheStorage is deprecated.', 16384);
		return $service;
	}


	/**
	 * @return Nette\Bridges\ApplicationLatte\TemplateFactory
	 */
	public function createServiceNette__templateFactory()
	{
		$service = new Nette\Bridges\ApplicationLatte\TemplateFactory($this->getService('nette.latteFactory'), $this->getService('httpRequest'), $this->getService('httpResponse'), $this->getService('user'), $this->getService('cacheStorage'));
		return $service;
	}


	/**
	 * @return Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage
	 */
	public function createServiceNette__userStorage()
	{
		$service = new Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage($this->getService('session'), $this->getService('doctrine.default.entityManager'));
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServicePartnersModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/partnersModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return Tomaj\Image\ImageService
	 */
	public function createServicePartnersModule__imageService()
	{
		$service = new \Tomaj\Image\ImageService($this->getService('imagesBackend'), 'partners/:year/:hash', array('200x125'), 70);
		if (!$service instanceof Tomaj\Image\ImageService) {
			throw new Nette\UnexpectedValueException('Unable to create service \'partnersModule.imageService\', value returned by factory is not Tomaj\\Image\\ImageService type.');
		}
		return $service;
	}


	/**
	 * @return App\PartnersModule\Model\Service\PartnerService
	 */
	public function createServicePartnersModule__partnerService()
	{
		$service = new App\PartnersModule\Model\Service\PartnerService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('partnersModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->setImageService($this->getService('partnersModule.imageService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\PartnersModule\\Model\\Service\\PartnerService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\PartnersModule\\Model\\Service\\PartnerService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\PartnersModule\\Model\\Service\\PartnerService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\PaymentsModule\Model\Listeners\ApplicationsListener
	 */
	public function createServicePaymentsModule__applicationsListener()
	{
		$service = new App\PaymentsModule\Model\Listeners\ApplicationsListener($this->getService('monolog.logger'));
		$service->setPaymentService($this->getService('paymentsModule.paymentService'));
		$service->setSeasonTaxService($this->getService('seasons.seasonTaxService'));
		$service->setSeasonApplicationService($this->getService('seasons.seasonAppService'));
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServicePaymentsModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/paymentsModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\PaymentsModule\Model\Service\PaymentService
	 */
	public function createServicePaymentsModule__paymentService()
	{
		$service = new App\PaymentsModule\Model\Service\PaymentService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('paymentsModule.cacheStorage'));
		$service->setUsersService($this->getService('users.userService'));
		$service->setSeasonService($this->getService('seasons.seasonService'));
		$service->setDefaultDueDate('1 month');
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\PaymentsModule\\Model\\Service\\PaymentService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\PaymentsModule\\Model\\Service\\PaymentService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\PaymentsModule\\Model\\Service\\PaymentService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Kdyby\PresenterTree
	 */
	public function createServicePresenterTree()
	{
		$service = new Kdyby\PresenterTree($this->getService('cacheStorage'), $this->getService('robotLoader'));
		return $service;
	}


	/**
	 * @return Nette\Loaders\RobotLoader
	 */
	public function createServiceRobotLoader()
	{
		$service = new Nette\Loaders\RobotLoader;
		$service->setCacheStorage($this->getService('cacheStorage'));
		$service->addDirectory('/home/fuca/Projects/www/sportsclub/tests/../vendor/others');
		$service->addDirectory('/home/fuca/Projects/www/sportsclub/tests');
		$service->register();
		return $service;
	}


	/**
	 * @return Nette\Application\IRouter
	 */
	public function createServiceRouter()
	{
		$service = $this->getService('159_App_RouterFactory')->createRouter();
		if (!$service instanceof Nette\Application\IRouter) {
			throw new Nette\UnexpectedValueException('Unable to create service \'router\', value returned by factory is not Nette\\Application\\IRouter type.');
		}
		return $service;
	}


	/**
	 * @return App\SeasonsModule\Presenters\AdminPresenter
	 */
	public function createServiceSeasons__adminPresenter()
	{
		$service = new App\SeasonsModule\Presenters\AdminPresenter;
		$service->setConfig(array('memberShip' => TRUE));
		$service->onShutdown = $this->getService('events.manager')->createEvent(array(
			'Nette\\Application\\UI\\Presenter',
			'onShutdown',
		), $service->onShutdown);
		return $service;
	}


	/**
	 * @return App\SeasonsModule\Model\Service\SeasonApplicationService
	 */
	public function createServiceSeasons__seasonAppService()
	{
		$service = new App\SeasonsModule\Model\Service\SeasonApplicationService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('seasonsModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setSeasonService($this->getService('seasons.seasonService'));
		$service->setSeasonTaxService($this->getService('seasons.seasonTaxService'));
		$service->setPaymentService($this->getService('paymentsModule.paymentService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonApplicationService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonApplicationService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonApplicationService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\SeasonsModule\Model\Service\SeasonService
	 */
	public function createServiceSeasons__seasonService()
	{
		$service = new App\SeasonsModule\Model\Service\SeasonService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('seasonsModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\SeasonsModule\Model\Service\SeasonTaxService
	 */
	public function createServiceSeasons__seasonTaxService()
	{
		$service = new App\SeasonsModule\Model\Service\SeasonTaxService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('seasonsModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setSeasonService($this->getService('seasons.seasonService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonTaxService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonTaxService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SeasonsModule\\Model\\Service\\SeasonTaxService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceSeasonsModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/seasonsModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\SecurityModule\Model\Listeners\AclRuleListener
	 */
	public function createServiceSecurityModule__aclRuleListener()
	{
		$service = new App\SecurityModule\Model\Listeners\AclRuleListener($this->getService('monolog.logger'), $this->getService('securityModule.aclService'));
		return $service;
	}


	/**
	 * @return App\Model\Service\AclRuleService
	 */
	public function createServiceSecurityModule__aclRulesService()
	{
		$service = new App\Model\Service\AclRuleService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('securityModule.cacheStorage'));
		$service->setRoleService($this->getService('securityModule.rolesService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\Model\\Service\\AclRuleService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\Model\\Service\\AclRuleService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\Model\\Service\\AclRuleService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\SecurityModule\Model\Service\AclService
	 */
	public function createServiceSecurityModule__aclService()
	{
		$service = new App\SecurityModule\Model\Service\AclService($this->getService('doctrine.default.entityManager'));
		$service->setCacheStorage($this->getService('securityModule.cacheStorage'));
		$service->setRolesService($this->getService('securityModule.rolesService'));
		$service->setRulesService($this->getService('securityModule.aclRulesService'));
		$service->setResourcesService($this->getService('securityModule.resourceService'));
		return $service;
	}


	/**
	 * @return App\SecurityModule\Model\Listeners\ApplicationsListener
	 */
	public function createServiceSecurityModule__applicationsListener()
	{
		$service = new App\SecurityModule\Model\Listeners\ApplicationsListener($this->getService('monolog.logger'));
		$service->setPositionService($this->getService('securityModule.positionsService'));
		$service->setRoleService($this->getService('securityModule.rolesService'));
		$service->setDefaultRoleName('player');
		$service->setDefaultComment('Created by system');
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceSecurityModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/securityModule/', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\SecurityModule\Config\Initializer
	 */
	public function createServiceSecurityModule__initializer()
	{
		$service = new App\SecurityModule\Config\Initializer($this->getService('systemModule.sportGroupService'), $this->getService('securityModule.positionsService'), $this->getService('users.userService'), $this->getService('securityModule.rolesService'), $this->getService('securityModule.aclRulesService'), $this->getService('monolog.logger'));
		$service->setRolesValues(array(
			'admin',
			'player',
			4 => 'guest',
			'member',
			'executive',
			9 => 'authenticated',
		));
		$service->setDefaultUserEmail('michal.fuca.fucik@gmail.com');
		$service->rolesInit();
		$service->positionsInit();
		$service->rulesInit();
		return $service;
	}


	/**
	 * @return App\SecurityModule\Model\Service\PositionService
	 */
	public function createServiceSecurityModule__positionsService()
	{
		$service = new App\SecurityModule\Model\Service\PositionService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('securityModule.cacheStorage'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setUserService($this->getService('users.userService'));
		$service->setRoleService($this->getService('securityModule.rolesService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SecurityModule\\Model\\Service\\PositionService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SecurityModule\\Model\\Service\\PositionService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SecurityModule\\Model\\Service\\PositionService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\SecurityModule\Model\Service\ResourceService
	 */
	public function createServiceSecurityModule__resourceService()
	{
		$service = new App\SecurityModule\Model\Service\ResourceService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('securityModule.cacheStorage'));
		$service->setPresenterTree($this->getService('presenterTree'));
		$service->setAnnotationsReader($this->getService('annotations.reader'));
		return $service;
	}


	/**
	 * @return App\Model\Service\RoleService
	 */
	public function createServiceSecurityModule__rolesService()
	{
		$service = new App\Model\Service\RoleService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('securityModule.cacheStorage'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\Model\\Service\\RoleService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\Model\\Service\\RoleService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\Model\\Service\\RoleService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return Nette\Http\Session
	 */
	public function createServiceSession()
	{
		$service = new Nette\Http\Session($this->getService('httpRequest'), $this->getService('httpResponse'));
		$service->setExpiration('14 days');
		$service->setOptions(array(
			'save_path' => '/home/fuca/Projects/www/sportsclub/tests/tmp/sessions',
			'name' => 'sportsclub',
		));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\Menu\AdminMenuControlFactory
	 */
	public function createServiceSystemModule__adminMenuControlFactory()
	{
		$service = new App\SystemModule\Model\Service\Menu\AdminMenuControlFactory($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'systemModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':System:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'systemModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'usersModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Users:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'usersModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'securityModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Security:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'securityModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'seasonsModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Seasons:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'seasonsModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'paymentsModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Payments:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'paymentsModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'eventsModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Events:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'eventsModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'articlesModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Articles:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'articlesModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'wallsModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Walls:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'wallsModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'communicationModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Communication:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'communicationModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'motivationModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Motivation:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'motivationModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'partnersModule.adminMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Partners:Admin:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'partnersModule.adminMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\ApplicationsListener
	 */
	public function createServiceSystemModule__applicationListener()
	{
		$service = new App\SystemModule\Model\Listeners\ApplicationsListener($this->getService('monolog.logger'), $this->getService('systemModule.notificationService'));
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceSystemModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/systemModule/', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\Menu\CategoriesMenuFactory
	 */
	public function createServiceSystemModule__categoriesMenuFactory()
	{
		$service = new App\SystemModule\Model\Service\Menu\CategoriesMenuFactory($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->setTranslator($this->getService('translation.default'));
		$service->setSportGroupsService($this->getService('systemModule.sportGroupService'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\CommentService
	 */
	public function createServiceSystemModule__commentService()
	{
		$service = new App\SystemModule\Model\Service\CommentService($this->getService('doctrine.default.entityManager'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\CommentService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\CommentService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\CommentService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\Menu\CommonMenuControlFactory
	 */
	public function createServiceSystemModule__commonMenuControlFactory()
	{
		$service = new App\SystemModule\Model\Service\Menu\CommonMenuControlFactory($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'eventsModule.clubMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Events:Club:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'eventsModule.clubMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'wallsModule.protectedMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Walls:Protected:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'wallsModule.protectedMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'communicationModule.protectedForumMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Communication:Forum:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'communicationModule.protectedForumMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		return $service;
	}


	/**
	 * @return App\SystemModule\Config\Initializer
	 */
	public function createServiceSystemModule__initializer()
	{
		$service = new App\SystemModule\Config\Initializer($this->getService('systemModule.sportGroupService'), $this->getService('monolog.logger'));
		$service->setGroupValues(array(
			'name' => 'Club',
			'description' => 'Root system group',
			'abbr' => 'root',
			'priority' => 10,
			'activity' => TRUE,
		));
		$service->groupInit();
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\MessagesListener
	 */
	public function createServiceSystemModule__messagesListener()
	{
		$service = new App\SystemModule\Model\Listeners\MessagesListener($this->getService('monolog.logger'), $this->getService('systemModule.notificationService'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\EmailNotificationService
	 */
	public function createServiceSystemModule__notificationService()
	{
		$service = new App\SystemModule\Model\Service\EmailNotificationService($this->getService('monolog.logger'), $this->getService('translation.default'));
		$service->setHostName('FBC Mohelnice');
		$service->setSenderEmail('misan.128@seznam.cz');
		$service->setSmtpOptions(array());
		$service->setDesiredMailerType(App\SystemModule\Model\Service\EmailNotificationService::MAILER_TYPE_SEND);
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\PaymentsListener
	 */
	public function createServiceSystemModule__paymentsListener()
	{
		$service = new App\SystemModule\Model\Listeners\PaymentsListener($this->getService('monolog.logger'), $this->getService('systemModule.notificationService'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\Menu\ProtectedMenuControlFactory
	 */
	public function createServiceSystemModule__protectedMenuControlFactory()
	{
		$service = new App\SystemModule\Model\Service\Menu\ProtectedMenuControlFactory($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'usersModule.protectedMenuDataItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Users:User:data',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'usersModule.protectedMenuDataItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'usersModule.protectedMenuProfileItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Users:User:profile',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'usersModule.protectedMenuProfileItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'securityModule.protectedMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Security:Auth:out',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'separate' => TRUE,
				'headOnly' => TRUE,
				'desc' => 'securityModule.protectedMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'paymentsModule.protectedMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Payments:User:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'paymentsModule.protectedMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'eventsModule.userMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Events:User:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'eventsModule.userMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'communicationModule.protectedMessagesMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Communication:Messaging:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'communicationModule.protectedMessagesMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'motivationModule.protectedMenuItem.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Motivation:Protected:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'motivationModule.protectedMenuItem.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\Menu\PublicMenuControlFactory
	 */
	public function createServiceSystemModule__publicMenuControlFactory()
	{
		$service = new App\SystemModule\Model\Service\Menu\PublicMenuControlFactory($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setSportTypeService($this->getService('systemModule.sportTypeService'));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'securityModule.public.menu.contacts.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Security:Public:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'securityModule.public.menu.contacts.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		$service->addItem(Nette\PhpGenerator\Helpers::createObject('App\SystemModule\Model\Service\Menu\ItemData', array(
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00name" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00label" => 'articlesModule.publicMenu.articles.label',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00url" => ':Articles:Public:default',
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00mode" => NULL,
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00data" => array(
				'desc' => 'articlesModule.publicMenu.articles.description',
			),
			"\x00App\\SystemModule\\Model\\Service\\Menu\\ItemData\x00children" => Nette\PhpGenerator\Helpers::createObject('Doctrine\Common\Collections\ArrayCollection', array(
				"\x00Doctrine\\Common\\Collections\\ArrayCollection\x00_elements" => array(),
			)),
		)));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\SportGroupListener
	 */
	public function createServiceSystemModule__sportGroupListener()
	{
		$service = new App\SystemModule\Model\Listeners\SportGroupListener($this->getService('monolog.logger'));
		$service->setPublicMenuFactory($this->getService('systemModule.publicMenuControlFactory'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\SportGroupService
	 */
	public function createServiceSystemModule__sportGroupService()
	{
		$service = new App\SystemModule\Model\Service\SportGroupService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\SportGroupService',
			'onCreate',
		), $service->onCreate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\SportGroupService',
			'onDelete',
		), $service->onDelete);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\SportGroupService',
			'onUpdate',
		), $service->onUpdate);
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\SportTypeListener
	 */
	public function createServiceSystemModule__sportTypeListener()
	{
		$service = new App\SystemModule\Model\Listeners\SportTypeListener($this->getService('monolog.logger'));
		$service->setPublicMenuFactory($this->getService('systemModule.publicMenuControlFactory'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\SportTypeService
	 */
	public function createServiceSystemModule__sportTypeService()
	{
		$service = new App\SystemModule\Model\Service\SportTypeService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\SportTypeService',
			'onCreate',
		), $service->onCreate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\SportTypeService',
			'onDelete',
		), $service->onDelete);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\SportTypeService',
			'onUpdate',
		), $service->onUpdate);
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\StaticPageListener
	 */
	public function createServiceSystemModule__staticPageListener()
	{
		$service = new App\SystemModule\Model\Listeners\StaticPageListener($this->getService('monolog.logger'));
		$service->setPublicMenuFactory($this->getService('systemModule.publicMenuControlFactory'));
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Service\StaticPageService
	 */
	public function createServiceSystemModule__staticPageService()
	{
		$service = new App\SystemModule\Model\Service\StaticPageService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('systemModule.cacheStorage'));
		$service->setUserService($this->getService('users.userService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\StaticPageService',
			'onCreate',
		), $service->onCreate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\StaticPageService',
			'onDelete',
		), $service->onDelete);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\SystemModule\\Model\\Service\\StaticPageService',
			'onUpdate',
		), $service->onUpdate);
		return $service;
	}


	/**
	 * @return App\SystemModule\Model\Listeners\UsersListener
	 */
	public function createServiceSystemModule__userListener()
	{
		$service = new App\SystemModule\Model\Listeners\UsersListener($this->getService('monolog.logger'), $this->getService('systemModule.notificationService'));
		return $service;
	}


	/**
	 * @return Kdyby\Translation\CatalogueCompiler
	 */
	public function createServiceTranslation__catalogueCompiler()
	{
		$service = new Kdyby\Translation\CatalogueCompiler(new Kdyby\Translation\Caching\PhpFileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache', $this->getService('nette.cacheJournal')), $this->getService('translation.fallbackResolver'), $this->getService('translation.catalogueFactory'), $this->getService('translation.loadersInitializer'));
		$service->enableDebugMode();
		return $service;
	}


	/**
	 * @return Kdyby\Translation\CatalogueFactory
	 */
	public function createServiceTranslation__catalogueFactory()
	{
		$service = new Kdyby\Translation\CatalogueFactory($this->getService('translation.fallbackResolver'), $this->getService('translation.loader'));
		return $service;
	}


	/**
	 * @return Kdyby\Translation\Console\ExtractCommand
	 */
	public function createServiceTranslation__console__extract()
	{
		$service = new Kdyby\Translation\Console\ExtractCommand;
		$service->defaultOutputDir = '/home/fuca/Projects/www/sportsclub/tests/lang';
		return $service;
	}


	/**
	 * @return Kdyby\Translation\Translator
	 */
	public function createServiceTranslation__default()
	{
		$service = new Kdyby\Translation\Translator($this->getService('translation.userLocaleResolver'), $this->getService('translation.selector'), $this->getService('translation.catalogueCompiler'), $this->getService('translation.catalogueFactory'), $this->getService('translation.fallbackResolver'));
		$this->getService('translation.userLocaleResolver.param')->setTranslator($service);
		$service->setFallbackLocales(array('cs_CZ', 'cs'));
		$this->getService('translation.panel')->register($service);
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/systemModule.cs_CZ.neon', 'cs_CZ', 'systemModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/system.en_US.neon', 'en_US', 'system');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/system.cs_CZ.neon', 'cs_CZ', 'system');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/systemModule.en_US.neon', 'en_US', 'systemModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/UsersModule/config/../locale/usersModule.cs_CZ.neon', 'cs_CZ', 'usersModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/UsersModule/config/../locale/usersModule.en_US.neon', 'en_US', 'usersModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SecurityModule/config/../locale/securityModule.en_US.neon', 'en_US', 'securityModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SecurityModule/config/../locale/securityModule.cs_CZ.neon', 'cs_CZ', 'securityModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SeasonsModule/config/../locale/seasonsModule.cs_CZ.neon', 'cs_CZ', 'seasonsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SeasonsModule/config/../locale/seasonsModule.en_US.neon', 'en_US', 'seasonsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/PaymentsModule/config/../locale/paymentsModule.en_US.neon', 'en_US', 'paymentsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/EventsModule/config/../locale/eventsModule.en_US.neon', 'en_US', 'eventsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/EventsModule/config/../locale/eventsModule.cs_CZ.neon', 'cs_CZ', 'eventsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/ArticlesModule/config/../locale/articlesModule.en_US.neon', 'en_US', 'articlesModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/ArticlesModule/config/../locale/articlesModule.cs_CZ.neon', 'cs_CZ', 'articlesModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/WallsModule/config/../locale/wallsModule.en_US.neon', 'en_US', 'wallsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/CommunicationModule/config/../locale/communitactionModule.cs_CZ.neon', 'cs_CZ', 'communitactionModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/CommunicationModule/config/../locale/communicationModule.en_US.neon', 'en_US', 'communicationModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/MotivationModule/config/../locale/motivationModule.en_US.neon', 'en_US', 'motivationModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/PartnersModule/config/../locale/partnersModule.en_US.neon', 'en_US', 'partnersModule');
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\CsvFileDumper
	 */
	public function createServiceTranslation__dumper__csv()
	{
		$service = new Symfony\Component\Translation\Dumper\CsvFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\IniFileDumper
	 */
	public function createServiceTranslation__dumper__ini()
	{
		$service = new Symfony\Component\Translation\Dumper\IniFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\MoFileDumper
	 */
	public function createServiceTranslation__dumper__mo()
	{
		$service = new Symfony\Component\Translation\Dumper\MoFileDumper;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\Dumper\NeonFileDumper
	 */
	public function createServiceTranslation__dumper__neon()
	{
		$service = new Kdyby\Translation\Dumper\NeonFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\PhpFileDumper
	 */
	public function createServiceTranslation__dumper__php()
	{
		$service = new Symfony\Component\Translation\Dumper\PhpFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\PoFileDumper
	 */
	public function createServiceTranslation__dumper__po()
	{
		$service = new Symfony\Component\Translation\Dumper\PoFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\QtFileDumper
	 */
	public function createServiceTranslation__dumper__qt()
	{
		$service = new Symfony\Component\Translation\Dumper\QtFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\IcuResFileDumper
	 */
	public function createServiceTranslation__dumper__res()
	{
		$service = new Symfony\Component\Translation\Dumper\IcuResFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\XliffFileDumper
	 */
	public function createServiceTranslation__dumper__xliff()
	{
		$service = new Symfony\Component\Translation\Dumper\XliffFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Dumper\YamlFileDumper
	 */
	public function createServiceTranslation__dumper__yml()
	{
		$service = new Symfony\Component\Translation\Dumper\YamlFileDumper;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Extractor\ChainExtractor
	 */
	public function createServiceTranslation__extractor()
	{
		$service = new Symfony\Component\Translation\Extractor\ChainExtractor;
		$service->addExtractor('latte', $this->getService('translation.extractor.latte'));
		return $service;
	}


	/**
	 * @return Kdyby\Translation\Extractors\LatteExtractor
	 */
	public function createServiceTranslation__extractor__latte()
	{
		$service = new Kdyby\Translation\Extractors\LatteExtractor;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\FallbackResolver
	 */
	public function createServiceTranslation__fallbackResolver()
	{
		$service = new Kdyby\Translation\FallbackResolver;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\TemplateHelpers
	 */
	public function createServiceTranslation__helpers()
	{
		$service = $this->getService('translation.default')->createTemplateHelpers();
		if (!$service instanceof Kdyby\Translation\TemplateHelpers) {
			throw new Nette\UnexpectedValueException('Unable to create service \'translation.helpers\', value returned by factory is not Kdyby\\Translation\\TemplateHelpers type.');
		}
		return $service;
	}


	/**
	 * @return Kdyby\Translation\TranslationLoader
	 */
	public function createServiceTranslation__loader()
	{
		$service = new Kdyby\Translation\TranslationLoader;
		$service->addLoader('php', $this->getService('translation.loader.php'));
		$service->addLoader('yml', $this->getService('translation.loader.yml'));
		$service->addLoader('xlf', $this->getService('translation.loader.xlf'));
		$service->addLoader('po', $this->getService('translation.loader.po'));
		$service->addLoader('mo', $this->getService('translation.loader.mo'));
		$service->addLoader('ts', $this->getService('translation.loader.ts'));
		$service->addLoader('csv', $this->getService('translation.loader.csv'));
		$service->addLoader('res', $this->getService('translation.loader.res'));
		$service->addLoader('dat', $this->getService('translation.loader.dat'));
		$service->addLoader('ini', $this->getService('translation.loader.ini'));
		$service->addLoader('neon', $this->getService('translation.loader.neon'));
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\CsvFileLoader
	 */
	public function createServiceTranslation__loader__csv()
	{
		$service = new Symfony\Component\Translation\Loader\CsvFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\IcuDatFileLoader
	 */
	public function createServiceTranslation__loader__dat()
	{
		$service = new Symfony\Component\Translation\Loader\IcuDatFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\IniFileLoader
	 */
	public function createServiceTranslation__loader__ini()
	{
		$service = new Symfony\Component\Translation\Loader\IniFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\MoFileLoader
	 */
	public function createServiceTranslation__loader__mo()
	{
		$service = new Symfony\Component\Translation\Loader\MoFileLoader;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\Loader\NeonFileLoader
	 */
	public function createServiceTranslation__loader__neon()
	{
		$service = new Kdyby\Translation\Loader\NeonFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\PhpFileLoader
	 */
	public function createServiceTranslation__loader__php()
	{
		$service = new Symfony\Component\Translation\Loader\PhpFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\PoFileLoader
	 */
	public function createServiceTranslation__loader__po()
	{
		$service = new Symfony\Component\Translation\Loader\PoFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\IcuResFileLoader
	 */
	public function createServiceTranslation__loader__res()
	{
		$service = new Symfony\Component\Translation\Loader\IcuResFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\QtFileLoader
	 */
	public function createServiceTranslation__loader__ts()
	{
		$service = new Symfony\Component\Translation\Loader\QtFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\XliffFileLoader
	 */
	public function createServiceTranslation__loader__xlf()
	{
		$service = new Symfony\Component\Translation\Loader\XliffFileLoader;
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Loader\YamlFileLoader
	 */
	public function createServiceTranslation__loader__yml()
	{
		$service = new Symfony\Component\Translation\Loader\YamlFileLoader;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\LoadersInitializer
	 */
	public function createServiceTranslation__loadersInitializer()
	{
		$service = new Kdyby\Translation\LoadersInitializer(array(
			'translation.loader.php' => array('php'),
			'translation.loader.yml' => array('yml'),
			'translation.loader.xlf' => array('xlf'),
			'translation.loader.po' => array('po'),
			'translation.loader.mo' => array('mo'),
			'translation.loader.ts' => array('ts'),
			'translation.loader.csv' => array('csv'),
			'translation.loader.res' => array('res'),
			'translation.loader.dat' => array('dat'),
			'translation.loader.ini' => array('ini'),
			'translation.loader.neon' => array('neon'),
		), $this);
		return $service;
	}


	/**
	 * @return Kdyby\Translation\Diagnostics\Panel
	 */
	public function createServiceTranslation__panel()
	{
		$service = new Kdyby\Translation\Diagnostics\Panel('/home/fuca/Projects/www/sportsclub');
		$service->setResourceWhitelist(array('cs', 'en', 'de'));
		$service->setLocaleResolvers(array(
			$this->getService('translation.userLocaleResolver.session'),
			$this->getService('translation.userLocaleResolver.acceptHeader'),
			new Kdyby\Translation\LocaleResolver\DefaultLocale('en'),
		));
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/systemModule.cs_CZ.neon', 'cs_CZ', 'systemModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/system.en_US.neon', 'en_US', 'system');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/system.cs_CZ.neon', 'cs_CZ', 'system');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SystemModule/config/../locale/systemModule.en_US.neon', 'en_US', 'systemModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/UsersModule/config/../locale/usersModule.cs_CZ.neon', 'cs_CZ', 'usersModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/UsersModule/config/../locale/usersModule.en_US.neon', 'en_US', 'usersModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SecurityModule/config/../locale/securityModule.en_US.neon', 'en_US', 'securityModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SecurityModule/config/../locale/securityModule.cs_CZ.neon', 'cs_CZ', 'securityModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SeasonsModule/config/../locale/seasonsModule.cs_CZ.neon', 'cs_CZ', 'seasonsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/SeasonsModule/config/../locale/seasonsModule.en_US.neon', 'en_US', 'seasonsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/PaymentsModule/config/../locale/paymentsModule.en_US.neon', 'en_US', 'paymentsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/EventsModule/config/../locale/eventsModule.en_US.neon', 'en_US', 'eventsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/EventsModule/config/../locale/eventsModule.cs_CZ.neon', 'cs_CZ', 'eventsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/ArticlesModule/config/../locale/articlesModule.en_US.neon', 'en_US', 'articlesModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/ArticlesModule/config/../locale/articlesModule.cs_CZ.neon', 'cs_CZ', 'articlesModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/WallsModule/config/../locale/wallsModule.en_US.neon', 'en_US', 'wallsModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/CommunicationModule/config/../locale/communitactionModule.cs_CZ.neon', 'cs_CZ', 'communitactionModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/CommunicationModule/config/../locale/communicationModule.en_US.neon', 'en_US', 'communicationModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/MotivationModule/config/../locale/motivationModule.en_US.neon', 'en_US', 'motivationModule');
		$service->addResource('neon', '/home/fuca/Projects/www/sportsclub/app/modules/PartnersModule/config/../locale/partnersModule.en_US.neon', 'en_US', 'partnersModule');
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\MessageSelector
	 */
	public function createServiceTranslation__selector()
	{
		$service = new Symfony\Component\Translation\MessageSelector;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\IUserLocaleResolver
	 */
	public function createServiceTranslation__userLocaleResolver()
	{
		$service = new Kdyby\Translation\LocaleResolver\ChainResolver;
		if (!$service instanceof Kdyby\Translation\IUserLocaleResolver) {
			throw new Nette\UnexpectedValueException('Unable to create service \'translation.userLocaleResolver\', value returned by factory is not Kdyby\\Translation\\IUserLocaleResolver type.');
		}
		$service->addResolver(new Kdyby\Translation\LocaleResolver\DefaultLocale('en'));
		$service->addResolver($this->getService('translation.userLocaleResolver.acceptHeader'));
		$service->addResolver($this->getService('translation.userLocaleResolver.session'));
		return $service;
	}


	/**
	 * @return Kdyby\Translation\LocaleResolver\AcceptHeaderResolver
	 */
	public function createServiceTranslation__userLocaleResolver__acceptHeader()
	{
		$service = new Kdyby\Translation\LocaleResolver\AcceptHeaderResolver($this->getService('httpRequest'));
		return $service;
	}


	/**
	 * @return Kdyby\Translation\LocaleResolver\LocaleParamResolver
	 */
	public function createServiceTranslation__userLocaleResolver__param()
	{
		$service = new Kdyby\Translation\LocaleResolver\LocaleParamResolver;
		return $service;
	}


	/**
	 * @return Kdyby\Translation\LocaleResolver\SessionResolver
	 */
	public function createServiceTranslation__userLocaleResolver__session()
	{
		$service = new Kdyby\Translation\LocaleResolver\SessionResolver($this->getService('session'), $this->getService('httpResponse'));
		return $service;
	}


	/**
	 * @return Symfony\Component\Translation\Writer\TranslationWriter
	 */
	public function createServiceTranslation__writer()
	{
		$service = new Symfony\Component\Translation\Writer\TranslationWriter;
		$service->addDumper('php', $this->getService('translation.dumper.php'));
		$service->addDumper('xliff', $this->getService('translation.dumper.xliff'));
		$service->addDumper('po', $this->getService('translation.dumper.po'));
		$service->addDumper('mo', $this->getService('translation.dumper.mo'));
		$service->addDumper('yml', $this->getService('translation.dumper.yml'));
		$service->addDumper('neon', $this->getService('translation.dumper.neon'));
		$service->addDumper('qt', $this->getService('translation.dumper.qt'));
		$service->addDumper('csv', $this->getService('translation.dumper.csv'));
		$service->addDumper('ini', $this->getService('translation.dumper.ini'));
		$service->addDumper('res', $this->getService('translation.dumper.res'));
		return $service;
	}


	/**
	 * @return Nette\Security\User
	 */
	public function createServiceUser()
	{
		$service = new Nette\Security\User($this->getService('nette.userStorage'), $this->getService('authenticator'), $this->getService('securityModule.aclService'));
		$sl = $this; $service->onLoggedOut[] = function () use ($sl) { $sl->getService('facebook.session')->clearAll(); };
		$service->onLoggedIn = $this->getService('events.manager')->createEvent(array('Nette\\Security\\User', 'onLoggedIn'), $service->onLoggedIn);
		$service->onLoggedOut = $this->getService('events.manager')->createEvent(array('Nette\\Security\\User', 'onLoggedOut'), $service->onLoggedOut);
		return $service;
	}


	/**
	 * @return Tomaj\Image\ImageService
	 */
	public function createServiceUserImageService()
	{
		$service = new \Tomaj\Image\ImageService($this->getService('imagesBackend'), 'users/:year/:hash', array('230x280'), 80);
		if (!$service instanceof Tomaj\Image\ImageService) {
			throw new Nette\UnexpectedValueException('Unable to create service \'userImageService\', value returned by factory is not Tomaj\\Image\\ImageService type.');
		}
		return $service;
	}


	/**
	 * @return App\UsersModule\Model\Service\UserService
	 */
	public function createServiceUsers__userService()
	{
		$service = new App\UsersModule\Model\Service\UserService($this->getService('doctrine.default.entityManager'));
		$service->setCacheStorage($this->getService('usersModule.cacheStorage'));
		$service->setSalt('$2a06$05IKqFG8iuPts/cr0.');
		$service->setLogger($this->getService('monolog.logger'));
		$service->setImageService($this->getService('userImageService'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\UsersModule\\Model\\Service\\UserService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\UsersModule\\Model\\Service\\UserService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\UsersModule\\Model\\Service\\UserService',
			'onDelete',
		), $service->onDelete);
		$service->onActivate = $this->getService('events.manager')->createEvent(array(
			'App\\UsersModule\\Model\\Service\\UserService',
			'onActivate',
		), $service->onActivate);
		$service->onDeactivate = $this->getService('events.manager')->createEvent(array(
			'App\\UsersModule\\Model\\Service\\UserService',
			'onDeactivate',
		), $service->onDeactivate);
		$service->onPasswordRegenerate = $this->getService('events.manager')->createEvent(array(
			'App\\UsersModule\\Model\\Service\\UserService',
			'onPasswordRegenerate',
		), $service->onPasswordRegenerate);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceUsersModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/usersModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\UsersModule\Config\Initializer
	 */
	public function createServiceUsersModule__initializer()
	{
		$service = new App\UsersModule\Config\Initializer($this->getService('users.userService'), $this->getService('monolog.logger'));
		$service->setUserValues(array(
			'name' => 'FBC',
			'surname' => 'Mohelnice, o.s.',
			'nick' => 'Informační systém',
			'password' => 'admin',
			'contact' => array(
				'address' => array(
					'city' => 'Mohelnice',
					'postCode' => '789 85',
					'street' => 'Masarykova',
					'number' => '546/25',
					'accountNumber' => '2500140367/2010',
				),
				'phone' => '420732504156',
				'email' => 'michal.fuca.fucik@gmail.com',
			),
		));
		$service->userInit();
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileStorage
	 */
	public function createServiceWallsModule__cacheStorage()
	{
		$service = new Nette\Caching\Storages\FileStorage('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/services/wallsModule', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return App\WallsModule\Model\Service\WallService
	 */
	public function createServiceWallsModule__wallService()
	{
		$service = new App\WallsModule\Model\Service\WallService($this->getService('doctrine.default.entityManager'), $this->getService('monolog.logger'));
		$service->setCacheStorage($this->getService('wallsModule.cacheStorage'));
		$service->setSportGroupService($this->getService('systemModule.sportGroupService'));
		$service->setUserService($this->getService('users.userService'));
		$service->setCommentService($this->getService('systemModule.commentService'));
		$service->setLogger($this->getService('monolog.logger'));
		$service->onCreate = $this->getService('events.manager')->createEvent(array(
			'App\\WallsModule\\Model\\Service\\WallService',
			'onCreate',
		), $service->onCreate);
		$service->onUpdate = $this->getService('events.manager')->createEvent(array(
			'App\\WallsModule\\Model\\Service\\WallService',
			'onUpdate',
		), $service->onUpdate);
		$service->onDelete = $this->getService('events.manager')->createEvent(array(
			'App\\WallsModule\\Model\\Service\\WallService',
			'onDelete',
		), $service->onDelete);
		return $service;
	}


	public function initialize()
	{
		date_default_timezone_set('Europe/Prague');
		Nette\Bridges\Framework\TracyBridge::initialize();
		$this->getService('events.manager')->createEvent(array('Nette\\DI\\Container', 'onInitialize'))->dispatch($this);
		Tracy\Debugger::$email = 'michal.fuca.fucik@gmail.com';
		Tracy\Debugger::$editor = 'sublime';
		Tracy\Debugger::$browser = 'chromium-browser';
		Tracy\Debugger::$strictMode = TRUE;
		Nette\Caching\Storages\FileStorage::$useDirectories = TRUE;
		$this->getByType("Nette\Http\Session")->exists() && $this->getByType("Nette\Http\Session")->start();
		header('X-Frame-Options: SAMEORIGIN');
		$this->getService('systemModule.initializer');
		$this->getService('usersModule.initializer');
		$this->getService('securityModule.initializer');
		header('X-Powered-By: Nette Framework');
		header('Content-Type: text/html; charset=utf-8');
		Nette\Utils\SafeStream::register();
		Nette\Reflection\AnnotationsParser::setCacheStorage($this->getByType("Nette\Caching\IStorage"));
		Nette\Reflection\AnnotationsParser::$autoRefresh = FALSE;
		Doctrine\Common\Annotations\AnnotationRegistry::registerLoader("class_exists");;
		Kdyby\Doctrine\Diagnostics\Panel::registerBluescreen($this);
		Kdyby\Doctrine\Proxy\ProxyAutoloader::create('/home/fuca/Projects/www/sportsclub/tests/tmp/proxies', 'Kdyby\\GeneratedProxy')->register();
		Nette\Diagnostics\Debugger::getBlueScreen()->collapsePaths[] = '/home/fuca/Projects/www/sportsclub/vendor/kdyby/doctrine/src/Kdyby/Doctrine';
		Nette\Diagnostics\Debugger::getBlueScreen()->collapsePaths[] = '/home/fuca/Projects/www/sportsclub/vendor/doctrine';
		Nette\Diagnostics\Debugger::getBlueScreen()->collapsePaths[] = '/home/fuca/Projects/www/sportsclub/tests/tmp/proxies';
		Kdyby\Translation\Diagnostics\Panel::registerBluescreen();
		\Tracy\Debugger::setLogger($this->getService('monolog.adapter'));
	}

}



final class SystemContainer_Kdyby_Doctrine_EntityDaoFactoryImpl_doctrine_daoFactory implements Kdyby\Doctrine\EntityDaoFactory
{

	private $container;


	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}


	public function create($entityName)
	{
		$service = $this->container->getService('doctrine.default.entityManager')->getDao($entityName);
		if (!$service instanceof Kdyby\Doctrine\EntityDao) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrine.daoFactory\', value returned by factory is not Kdyby\\Doctrine\\EntityDao type.');
		}
		return $service;
	}

}



final class SystemContainer_Nette_Bridges_ApplicationLatte_ILatteFactoryImpl_nette_latteFactory implements Nette\Bridges\ApplicationLatte\ILatteFactory
{

	private $container;


	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}


	public function create()
	{
		$service = new Latte\Engine;
		$service->setTempDirectory('/home/fuca/Projects/www/sportsclub/tests/tmp/cache/latte');
		$service->setAutoRefresh(FALSE);
		$service->setContentType('html');
		Kdyby\Translation\Latte\TranslateMacros::install($service->getCompiler());
		$service->addFilter('translate', array(
			$this->container->getService('translation.helpers'),
			'translate',
		));
		$service->addFilter('getTranslator', array(
			$this->container->getService('translation.helpers'),
			'getTranslator',
		));
		$service->onCompile = $this->container->getService('events.manager')->createEvent(array('Latte\\Engine', 'onCompile'), $service->onCompile);
		return $service;
	}

}
