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

namespace App\CommunicationModule\Presenters;
use \App\CommunicationModule\Forms\PrivateMessageForm,
    \App\SystemModule\Presenters\SystemUserPresenter,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \App\Model\Entities\PrivateMessage,
    \App\Model\Entities\MailBoxEntry,
    \App\Model\Misc\Exceptions,
    \Nette\Utils\ArrayHash,
    \Grido\Grid,
    \Nette\Utils\Html,
    \App\Model\Misc\Enum\FormMode;
    

/**
 * MessagingPresenter
 * @Secured(resource="MessagingUser")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class MessagingPresenter extends SystemUserPresenter {
    
    /**
     * @inject
     * @var \App\CommunicationModule\Model\Service\IPrivateMessageService
     */
    public $privateMessageService;
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $groupService;
   
    
    public function actionDefault() {
	$this->redirect("inbox");
    }
        
    public function actionInbox() {
	// render grid
    }
    
    public function actionOutbox() {
	// render grid
    }
    
    public function actionDeleted() {
	// render grid
    }
    
    public function actionCreateMessage() {
	// render form
    }
    
    public function actionReplyMessage($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument ($id);
	try {
	    $mDb = $this->privateMessageService->getEntry($id);
	    if ($mDb !== null) {
		$form = $this->getComponent("replyPrivateMessageForm");
		$subject = "Re:".$mDb->getMessage()->getSubject();
		$content = "\n\n\n==============\n".$mDb->getMessage()->getSent()->format(self::DATETIME_FORMAT)."\n------------------------\n".$mDb->getMessage()->getContent();
		$recipient = $mDb->getSender()->getId();
		$m = ["subject"=>$subject, "content"=>$content, "recipient"=>$recipient];
		$form->setDefaults($m);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
    }
    
    public function handleDeleteMessage($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument ($id);
	try {
	    $this->privateMessageService
		    ->deleteMessage($id, $this->getUser()->getIdentity());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }
    
    public function createMessage(ArrayHash $values) {
	$pm = new PrivateMessage((array) $values);
	$mb = new MailBoxEntry((array) $values);
	$mb->setMessage($pm);
	$mb->setSender($this->getUser()->getIdentity());
	try {
	    $this->privateMessageService->createEntry($mb);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function privateMessageFormSuccess(PrivateMessageForm $form) {
	$values = $form->getValues();
	switch($form->getMode()) {
	    case FormMode::CREATE_MODE:
		    $this->createMessage($values);
		break;
	    case FormMode::UPDATE_MODE:
		    $values->recipient = [$values->recipient];
		    $this->createMessage($values);
		break;
	}
    }
    
    public function createComponentCreatePrivateMessageForm($name) {
	$form = $this->preparePrivateMessageForm($name);
	$form->initialize();
	return $form;
    }
    
    public function createComponentReplyPrivateMessageForm($name) {
	$form = $this->preparePrivateMessageForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    private function preparePrivateMessageForm($name) {
	$form = new PrivateMessageForm($this, $name, $this->getTranslator());
	try {
	   $form->setUsers($this->userService->getSelectUsers(
		$this->getUser()->getIdentity()->getId()));
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	return $form;
    }
    
    private function prepareMailBoxGrid($name) {
	try {
	    $users = $this->userService->getSelectUsers();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	$grid = new Grid($this, $name);
	$grid->setTranslator($this->getTranslator());
	
	$grid->addColumnText("starred", "communicationModule.pmForm.starred")
		->setSortable()
		->setCustomRender($this->starredRender);
	
	$headerStar = $grid->getColumn('starred')->headerPrototype;
	$headerStar->class[] = 'center';
	$headerStar->style['width'] = '1%';
	
	$grid->addColumnText("sender", "communicationModule.pmForm.sender")
		->setTruncate(17)
		->setCustomRender($this->senderRender)
		->setSortable()
		->setFilterSelect([null=>null]+$users);
	
	$headerSender = $grid->getColumn('sender')->headerPrototype;
	$headerSender->class[] = 'center';
	$headerSender->style['width'] = '15%';
	
	$grid->addColumnText("subject", "communicationModule.pmForm.subject")
		->setCustomRender($this->subjectRender)
		->setSortable()
		->setFilterText();
	
	$headerSbj = $grid->getColumn('subject')->headerPrototype;
	$headerSbj->class[] = 'center';
	$headerSbj->style['width'] = '70%';
	
	$grid->addColumnDate("sent", "communicationModule.pmForm.delivered")
		->setCustomRender($this->sentRender)
		->setSortable();
	
	$headerSnt = $grid->getColumn('sent')->headerPrototype;
	$headerSnt->class[] = 'center';
	$headerSnt->style['width'] = '15%';
	
	$grid->setFilterRenderType($this->filterRenderType);
	return $grid;
    }
    
    public function subjectRender($el) {
	return \Nette\Utils\Html::el("span")
		->addAttributes(["title"=>$el->getMessage()->getSubject()])
		->setText(\Nette\Utils\Strings::truncate($el->getMessage()->getSubject(), 17));
    }
    
    public function sentRender($el) {
	return $el->getMessage()->getSent()->format(self::DATETIME_FORMAT);
    }
    
    public function senderRender($el) {
	return \Nette\Utils\Html::el("span")
		->addAttributes(["title"=>$el->getSender()])
		->setText(\Nette\Utils\Strings::truncate($el->getSender(), 17));
    }
    
    public function starredRender($el) {
	
	$star = $el->getStarred();
	
	if (!$star) {
	    $starIco = "star-empty";
	} else {
	    $starIco = "star";
	}
	return $a = Html::el("a")
		->addAttributes(["href"=>$this->link("toggleStarred!", $el->getId()),
		    "class"=>"glyphicon glyphicon-$starIco icon-$starIco"]);
    }
    
    public function handleToggleStarred($id) {
	// TODO 
    }
    
    public function createComponentInboxGrid($name) {
	$grid = $this->prepareMailBoxGrid($name);
	
	$grid->setModel($this->privateMessageService
		->getInboxDatasource($this->getUser()->getIdentity()));
	$grid->setOperation([
	    "delete"=>$this->tt("communicationModule.grid.delete"),
	    "read"=>$this->tt("communicationModule.grid.markAsRead"),
	    "unread"=>$this->tt("communicationModule.grid.markAsUnread"),
	    "starToggle"=>$this->tt("communicationModule.grid.starToggle")],
	    $this->gridOperationsHandler);
	
	$grid->addActionHref("reply", "", "replyMessage")
		->setIcon("repeat");
	return $grid;
    }
    
    public function gridOperationsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteMessage($id);
		}
		break;
	    case "read":
		foreach ($ids as $id) {
		    $this->doMarkAsReadMessage($id);
		}
		break;
	    case "unread":
		foreach ($ids as $id) {
		    $this->doMarkAsUnreadMessage($id);
		}
		break;
	    case "starToggle":
		foreach ($ids as $id) {
		    $this->doStarToggleMessage($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    private function doDeleteMessage($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument ($id);
	try {
	    $this->privateMessageService->deleteEntry($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($id, "this", $ex);
	}
    }
    
    private function doMarkAsReadMessage($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument ($id);
	try {
	    $this->privateMessageService->markAsRead($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($id, "this", $ex);
	}
    }
    
    private function doMarkAsUnreadMessage($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument ($id);
	try {
	    $this->privateMessageService->markAsUnread($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($id, "this", $ex);
	}
    }
    
    private function doStarToggleMessage($id) {
	
    }
    
    public function createComponentOutboxGrid($name) {
	$grid = $this->prepareMailBoxGrid($name);
	$grid->setModel($this->privateMessageService
		->getOutboxDatasource($this->getUser()->getIdentity()));
	$grid->setOperation([
	    "delete"=>$this->tt("communicationModule.grid.delete"),
	    "starToggle"=>$this->tt("communicationModule.grid.starToggle")],
	    $this->gridOperationsHandler);
	return $grid;
    }
    
    public function createComponentDeletedGrid($name) {
	$grid = $this->prepareMailBoxGrid($name);
	$grid->setModel($this->privateMessageService
		->getDeletedDatasource($this->getUser()->getIdentity()));
	$grid->setOperation([
	    "delete"=>$this->tt("communicationModule.grid.delete"),
	    "starToggle"=>$this->tt("communicationModule.grid.starToggle")],
	    $this->gridOperationsHandler);
	return $grid;
    }
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("communicationModule.messaging.inbox",":Communication:Messaging:inbox");
	$c->addNode("communicationModule.messaging.outbox",":Communication:Messaging:outbox");
	$c->addNode("communicationModule.messaging.deleted",":Communication:Messaging:deleted");
	$c->addNode("communicationModule.messaging.messageAdd",":Communication:Messaging:createMessage");
	$c->addNode("systemModule.navigation.back",":System:Default:userRoot");
	return $c;
    }
}