<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\SystemModule\Presenters;

use \Nette,
    \Smf\Menu,
    \Nette\InvalidArgumentException,
    \App\Model\Misc\Exceptions,
    \Nette\Application\UI\Presenter,
    \Grido\Components\Filters\Filter,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\SystemModule\Components\CommentControl,
    \App\SystemModule\Components\PartnersControl,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\SecurityModule\Components\LogInControl,
    \App\SystemModule\Model\Service\ICommentable,
    \App\SystemModule\Components\AppealControl;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

    const NUM_IDENTIFIER = 'id';
    const ROOT_GROUP = "root";
    
    /**
     * Absolute root of protected section, redirect there if module default action fails
     */
    const LAST_CHANCE_REDIRECT = ":System:Protected:default";

    /**
     * @const string Flash messages type
     */
    const FM_SUCCESS	= "alert alert-success fade in",
	    FM_ERROR	= "alert alert-danger fade in",
	    FM_WARNING	= "alert alert-warning fade in",
	    FM_INFO	= "alert alert-info";
    
    const DATETIME_FORMAT = "j.n.Y H:i",
	  DATE_FORMAT = "j.n.Y";

    /** @var string @persistent */
    public $ajax = 'on';

    /** @persistent */
    public $locale = 'en';

    /** @vat actual managinng entity id from parameter */
    private $entityId;

    /** @var \Kdyby\Doctrine\Entities\BaseEntity actual managing entity */
    private $entity;

    /**
     * @inject
     * @var \Kdyby\Translation\Translator
     */
    public $translator;

    /**
     * @inject
     * @var \Kdyby\Monolog\Logger
     */
    public $logger;
    
    /**
     * @inject
     * @var \Doctrine\Common\Annotations\Reader
     */
    public $annotationReader;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\Menu\CategoriesMenuFactory
     */
    public $catMenuFactory;
    
        
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\Menu\IAdminMenuControlFactory
     */
    public $adminMenuFactory;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\Menu\IProtectedMenuControlFactory
     */
    public $protectedMenuFactory;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\Menu\ICommonMenuControlFactory
     */
    public $commonMenuFactory;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\Menu\IPublicMenuControlFactory
     */
    public $publicMenuFactory;
    
    /**
     * @inject
     * @var \App\PartnersModule\Model\Service\IPartnerService
     */
    public $partnerService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ICommentService
     */
    public $commentService;
    
    
//    /**
//     * @inject
//     * @var \Brabijan\Images\ImagePipe
//     */
//    public $imagePipe;
    
    protected function getAnnotReader() {
	return $this->annotationReader;
    }

    /**
     * Recently managed entity id
     * @return mixed
     * @throws Exceptions\InvalidStateException
     */
    public function getEntityId() {
	if (!isset($this->entityId))
	    throw new Exceptions\InvalidStateException("EntityId is not set, it seems it wasn't passed within request '" . self::NUM_IDENTIFIER . "' parameter");
	return $this->entityId;
    }

    /**
     * Recently managed entity
     * @return \Kdyby\Doctrine\Entities\BaseEntity
     * @throws Exceptions\InvalidStateException
     */
    public function getEntity() {
	if (!isset($this->entity))
	    throw new Exceptions\InvalidStateException("Actual entity is not set, please use appropriate setter first");
	return $this->entity;
    }

    public function setEntity(BaseEntity $entity) {
	$this->entity = $entity;
    }

    public function getTranslator() {
	return $this->translator;
    }

    /**
     * Shortcut for translate given message via Translator service
     * @param string $message
     * @param int $count
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function tt($message, $count = null, $parameters = [], $domain = null, $locale = null) {
	return $this->getTranslator()->translate($message, $count, $parameters, $domain, $locale);
    }

    /**
     * Returns application locale detected by translator
     * @return string
     */
    public function getLocale() {
	return $this->getTranslator()->getLocale();
    }

    /** @var string */
    public $filterRenderType = Filter::RENDER_INNER;

    public function startup() {
	parent::startup();
	$this->entityId = $this->getParameter(self::NUM_IDENTIFIER);
	$this->locale = $this->getTranslator()->getLocale();
    }

    protected function createTemplate($class = NULL) {
	$template = parent::createTemplate($class);
	$template->registerHelperLoader(callback($this->translator->createTemplateHelpers(), 'loader'));
	$context = $this->context;
	$template->registerHelper('thumb', function($identifier, $type, $size = '') use ($context) {
	    $service = $context->getService($type."ImageService");
	    return \Tomaj\Image\Helper\Image::thumb($service, $identifier, $size);
	});
	$template->locale = $this->getTranslator()->getLocale();
	return $template;
    }

    protected function beforeRender() {
	parent::beforeRender();
	$this->setLayout('publicLayout');

	$this->template->setTranslator($this->translator);
	$appDir = $this->context->parameters['appDir'];
	$this->template->layoutsPath = $appDir . "/modules/SystemModule/templates/";
	$this->template->layoutStyle = \App\Model\Misc\Enum\LayoutSectionStyle::INFO;
	$this->template->breadCrumbSeparator = "/";
	$this->template->titleCrumbSeparator = "«";
	$this->template->dateFormat = self::DATE_FORMAT;
	$this->template->dateTimeFormat = self::DATETIME_FORMAT;
	//$this->template->_imagePipe = $this->imagePipe;
	if ($this->isAjax()) {
	    $this->redrawControl("flash");
	}
    }
    
        
    public function searchFormSuccess(Form $form) {
	$values = $form->getValues();
	switch ($form->getMode()) {
	    case FormMode::CREATE_MODE:
		$this->redirect(":System:Search:default", $values->keyword);
		break;
	    case FormMode::UPDATE_MODE:
		break;
	}
    }

    // <editor-fold desc="COMMON COMPONENT FACTORIES">

    protected function createComponentLoginControl($name) {
	$c = new LogInControl($this, $name);
	$c->setLogInTarget(":System:Homepage:default");
	return $c;
    }

    protected function createComponentCommentControl($name) {
	$c = new CommentControl($this, $name);
	$c->setEntity($this->getEntity());
	$c->setUser($this->getUser()->getIdentity());
	$c->setCommentService($this->commentService);
	//$c->setIsCommenting($this->isAllowedToComment($this->getEntity()));
	return $c;
    }
    
    protected function createComponentGroupsFilterMenu($name) {
	$c = $this->catMenuFactory->createComponent($this, $name);
	return $c;
    }
    
    protected function createComponentMailMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	return $c;
    }
    
    public function createComponentAdminMenu($name) {
	$c = $this->adminMenuFactory->createComponent($this, $name);
	return $c;
    }
    
    public function createComponentProtectedMenu($name) {
	$c = $this->protectedMenuFactory->createComponent($this, $name);
	return $c;
    }
    
    public function createComponentCommonMenu($name) {
	$c = $this->commonMenuFactory->createComponent($this, $name);
	return $c;
    }
    
    protected function createComponentPublicMenu($name) {
	$c = $this->publicMenuFactory->createComponent($this, $name);
	return $c;
    }
    
    protected function createComponentRss() {
	return new \RssControl();
    }
    
    protected function createComponentPartners($name) {
	$data = [];
	try {
	    $data = $this->partnerService->getActivePartners();
	} catch (Exception $ex) {
	    $this->handleDataLoad(null, null, $ex);
	}
	return new PartnersControl($this, $name, $data);
    }
    
    protected function createComponentSearchForm($name) {
	$c = new \App\SystemModule\Forms\SearchForm($this, $name, $this->getTranslator());
	$c->initialize();
	return $c;
    }
    
    protected function createComponentAppealControl($name) {
	$c = new AppealControl($this, $name, $this->getUser()->getIdentity());
	return $c;
    }

    // </editor-fold>
    // <editor-fold desc="LOGGING SUPPORT"> 

    private function prefixMessage($message, $type) {
	return "###   " . $type . "   ### " . $this->getName() . " -->  \n" . $message;
    }

    protected function logError($message, array $context = []) {
	$this->logger->addError($this->prefixMessage($message, "ERROR"), $context);
    }

    protected function logWarning($message, array $context = []) {
	$this->logger->addWarning($this->prefixMessage($message, "WARNING"), $context);
    }

    protected function logInfo($message, array $context = []) {
	$this->logger->addInfo($this->prefixMessage($message, "INFO"), $context);
    }

    protected function logDebug($message, array $context = []) {
	$this->logger->addDebug($this->prefixMessage($message, "DEBUG"), $context);
    }

    //</editor-fold>
    // <editor-fold desc="COMMON FLASH MESSAGES SUPPORT">

    protected function handleBadArgument($id, $redirect = null, $ex = null) {
	$this->handleError($id, $redirect, "system.messages.badArgumentFormat", $ex);
    }
    
    protected function handleDataLoad($id, $redirect = null, $ex = null) {
	$this->handleError($id, $redirect, "system.messages.couldNotLoadData", $ex);
    }
    
    protected function handleDataSave($id, $redirect = null, $ex = null) {
	$this->handleError($id, $redirect == null?"this":$redirect, "system.messages.couldNotSaveData", $ex);
    }
    
    protected function handleDataDelete($id, $redirect = null, $ex = null) {
	$this->handleError($id, $redirect == null?"this":$redirect, "system.messages.couldNotDeleteData", $ex);
    }
    
    protected function handleDependencyDelete($id, $redirect = null, $ex = null) {
	$this->handleError($id, $redirect, "system.admin.messages.dependencyErrorDelete", $ex);
    }
    
    protected function handleEntityNotExists($id, $redirect = null, $ex = null) {
	$this->handleWarning($id, $redirect, "system.admin.messages.entityNotExist", $ex);
    }
    
    private function handleError($id, $redirect, $message, \Exception $exception) {
	$this->handleProblem($id, $redirect, $message, $exception, self::FM_ERROR);
    }
    
    private function handleWarning($id, $redirect, $message, \Exception $exception) {
	$this->handleProblem($id, $redirect, $message, $exception, self::FM_WARNING);
    }
    
    private function handleProblem($id, $redirect, $message, \Exception $exception, $fmType) {
	$sig = $this->signal;
	$prefix = $this->getName() . " / " . $this->getAction() . " / " . $sig[1] . "! ";
	$m = $this->tt($message, !is_numeric($id)? null:10, ["id" => $id]);
	$this->flashMessage($m, $fmType);
	
	switch($fmType) {
	    case self::FM_ERROR:
		$this->logError($exception ? $exception : $prefix . $m);
		break;
	    case self::FM_WARNING:
		$this->logWarning($exception ? $exception : $prefix . $m);
		break;
	    case self::FM_SUCCESS:
		$this->logInfo($exception ? $exception : $prefix . $m);
		break;
	    default:
		$this->logDebug($exception ? $exception : $prefix . $m);
	}
	
	if ($redirect !== null)
	    $this->redirect($redirect);
    }
    

    // </editor-fold>
//    private function isAllowedToComment(ICommentable $e) {
//	// maybe this should be within authorizator or commentControl with given user instance
//	$mode = $e->getCommentMode();
//	switch ($mode) {
//	    case CommentMode::ALLOWED:
//		return true;
//		break;
//	    case CommentMode::RESTRICTED:
//		return false;
//		break;
//	    case CommentMode::SIGNED:
//		return $this->getUser()->isLoggedIn();
//		break;
//	    case CommentMode::GROUP:
//		$ug = $this->getUser()->getIdentity()->getGroups();
//		$eg = $e->getGroups();
//		return true;
//		foreach ($ug as $g) {		    
//		    // ptam se, jestli ta skupina je v eg
//		}
//		break;
//	}
//    }
}
