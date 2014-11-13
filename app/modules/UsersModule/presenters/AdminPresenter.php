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

namespace App\UsersModule\Presenters;

use \App\UsersModule\Forms\UserForm,
    \Grido\Grid,
    \Nette\Utils\Strings,
    \Nette\Mail\SendmailMailer,
    \App\Model\Entities\User,
    \App\Model\Entities\WebProfile,
    \App\Model\Entities\Address,
    \App\Model\Entities\Contact,
    \Nette\Mail\Message,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\FormMode,
    \Nette\DateTime,
    \Nette\ArrayHash,
    \App\UsersModule\Forms\WebProfileForm,
    \App\SystemModule\Presenters\SecuredPresenter,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \App\UsersModule\Model\Misc\Utils\UserEntityManageHelper;

/**
 * UserPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;

    /**
     * User admin presenter action of Default request
     */
    public function actionDefault() {
	// grid
    }

    // <editor-fold desc="ADD USER">

    /**
     * User admin presenter action of New user request
     */
    public function actionNewUser() {
	// form
    }

    /**
     * Create new user handler (topdown)
     * @param \Nette\ArrayHash $values
     * @throws DuplicateEntryException
     */
    public function createUser(ArrayHash $values) {


	// SEND NOTIFICATION
//	$mailer = new SendmailMailer();
//	$mail = new Message();
//	$mail->setFrom("sportsclub@gmail.com");
//	$mail->setSubject("New registration");
//	$mail->setContentType('text/html');
//	$mail->setBody("Vazeny uzivateli, byl vam vytvoren ucet v nasem sportovniho klubu XY\n\n Heslo: $newPassword a login vas email");
//	$mail->addTo("misan.128@seznam.cz");
//	$mailer->send($mail);

	$nu = $this->hydrateUserFromUserForm($values);
	try {
	    $this->userService->createUser($nu);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->getTranslator()->tt("usersModule.messages.createUserFailed");
	    $this->flashMessag($m, self::FM_ERROR);
	}
	$this->redirect("Admin:default");
    }

    // </editor-fold>
    // <editor-fold desc="REMOVE USER">

    /**
     * Delete user handler (topdown)
     * @param numeric $id
     */
    public function handleDeleteUser($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id, "Admin:default");
	$this->doDeleteUser($id);
	if (!$this->isAjax()) {
	    $this->redirect("this");
	}
    }

    private function doDeleteUser($id) {
	try {
	    $this->userService->deleteUser($id);
	} catch (Exceptions\DependencyException $ex) {
	    $this->logInfo($ex->getMessage());
	    $m = $this->getTranslator()->tt("usersModule.messages.dependencyErrorDelete");
	    $this->flashMessage($m, self::FM_WARNING);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->getTranslator()->tt("usersModule.admin.messages.deleteUserFailed", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
    }

    // </editor-fold>
    // <editor-fold desc="UPDATE USER">

    /**
     * Action for filling updateUserForm control by values from database
     * @Secured()
     * @param numeric $id
     */
    public function actionUpdateUser($id) {
	if ($id === null || !is_numeric($id)) $this->handleBadArgument($id, "default");
	try {
	    $uUser = $this->userService->getUser($id);
	    $form = $this->getComponent('updateUserForm');

	    $data = $uUser->toArray() + $uUser->getContact()->toArray() + $uUser->getContact()->getAddress()->toArray();
	    $form->setDefaults($data);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.messages.updateUserFailed", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
    }

    /**
     * Update user handler (topdown)
     * @param \Nette\ArrayHash $values
     */
    public function updateUser(ArrayHash $values) {
	try {
	    $this->userService->updateUser(UserEntityManageHelper::hydrateUserFromHash($values));
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.messages.updateUserFailed", ["id" => $values->id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
	$this->redirect("Admin:default");
    }

    /**
     * Component factory for lazy initialize of updateUserForm
     * @param string $name
     * @return \App\UsersModule\Forms\UserForm
     */
    public function createComponentUpdateUserForm($name) {
	$form = $this->prepareUserForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    /**
     * Component factory for lazy initialize of newUserForm
     * @param string $name
     * @return \App\UsersModule\Forms\UserForm
     */
    public function createComponentNewUserForm($name) {
	$form = $this->prepareUserForm($name);
	$form->initialize();
	return $form;
    }

    private function prepareUserForm($name) {
	$form = new UserForm($this, $name, $this->getTranslator());
	return $form;
    }

    public function handleRegenPassword($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id, "Admin:default");
	try {
	    if ($this->userService->regeneratePassword($id) != null) {
		$m = $this->tt("usersModule.messages.newPwSuccess", ["id" => $id]);
		$this->flashMessage($m);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $m = $this->tt("usersModule.messages.newPwFailed", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	    $this->logError($ex->getMessage());
	}
	$this->redirect('this');
    }

    private function doActiveToggle($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id, "Admin:default");
	try {
	    $this->userService->toggleUser($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.toggleUserFailed", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
    }

    // </editor-fold>
    // <editor-fold desc="Web profile manage">

    public function actionUpdateWebProfile($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id, "Admin:default");
	try {
	    $user = $this->userService->getUser($id);
	    $this->setEntity($user);
	    $wp = $user->getWebProfile();
	    $form = $this->getComponent("updateWebProfileForm");
	    $form->setDefaults($wp->toArray());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.cannotReadData", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
    }

    public function createComponentUpdateWebProfileForm($name) {
	$form = new WebProfileForm($this, $name, $this->getTranslator());
	$form->setShowCancel();
	$form->initialize();
	return $form;
    }

    public function webProfileFormSuccess(WebProfileForm $form) {
	$values = $form->getValues();
	try {
	    $dbUser = $this->userService->getUser($this->getEntity()->getId());
	    $wp = new WebProfile((array) $values);
	    $wp->setUpdated(new \Nette\Utils\DateTime());
	    $wp->setEditor($this->getUser()->getIdentity());
	    $dbUser->setWebProfile($wp);
	    $this->userService->updateUser($dbUser);
	} catch (Exceptions\DataErrorException $e) {
	    $this->logError($e);
	    $m = $this->tt("usersModule.admin.webProfileUpdateFailed", ["id" => $values["id"]]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
	$this->redirect("Admin:default");
    }

    // </editor-fold>
    // <editor-fold desc="Users grid">

    /**
     * Component factory of UsersGrid
     * @param string $name
     * @return \Grido\Grid
     */
    public function createComponentUsersGrid($name) {

	$grid = new Grid($this, $name);
	$grid->setModel($this->userService->getUsersDatasource());

	$grid->translator->lang = $this->getLocale();
	$grid->setDefaultPerPage(30);
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('surname', 'Příjmení')
		->setSortable()
		->setFilterText();
	//$grid->getColumn('surname')->getEditableControl()->setRequired('Surname is required.');
	$headerSurname = $grid->getColumn('surname')->headerPrototype;
	$headerSurname->class[] = 'center';

	$grid->addColumnText('name', 'Jméno')
		->setSortable()
		->setFilterText();
	//$grid->getColumn('name')->getEditableControl()->setRequired('Name is required.');
	$headerName = $grid->getColumn('name')->headerPrototype;
	$headerName->class[] = 'center';

//	$grid->addFilterCustom('nameOrSurname', new \Nette\Forms\Controls\TextArea('Jméno nebo příjmení'))
//		->setColumn('name')
//		->setColumn('surname', \Grido\Components\Filters\Condition::OPERATOR_OR)
//		->setCondition('LIKE ?')
//		->setFormatValue('%%value%');
	//$grid->getColumn('name')->getCellPrototype()->class('textleft');

	$y = $this->tt("system.common.yes");
	$n = $this->tt("system.common.no");
	$activeList = [null => null, true => $y, false => $n];
	$grid->addColumnNumber('active', 'Aktivní')
		->setReplacement(
		    [true => $y, 
		    null => $n])
		->setSortable()
		->setFilterSelect($activeList);

	$headerActive = $grid->getColumn('active')->headerPrototype;
	$headerActive->class[] = 'center';

	$grid->addColumnDate('lastLogin', 'Posl. přihl.', self::DATETIME_FORMAT)
		->setSortable()
		->setReplacement([NULL => $this->tt("usersModule.admin.grid.never")])
		->setFilterDateRange();
	$headerLast = $grid->getColumn('lastLogin')->headerPrototype;
	$headerLast->class[] = 'center';

	$grid->addColumnDate('created', 'Registrován')
		->setSortable();
	$headerCreated = $grid->getColumn('created')->headerPrototype;
	$headerCreated->class[] = 'center';

	//$grid->getColumn('lastLogin')->getCellPrototype()->class('textsmall');
//	$seas = $this->getActualSeasonId();
//	try {
//	    $seas = $this->getSeasonsModel()->getSeason($seas);
//	} catch (\Nette\IOException $ex) {
//	    Debugger::log($ex->getMessage(), Debugger::ERROR);
//	}
//	
//	if ($seas != FALSE)
//	    $seas = $seas->label;
//
//	if ($this->user->isAllowed("Admin:users", "update"))
//	    $grid->setOperations(array('deactivate' => 'De/aktivovat', 'application' => 'Přihlásit na sezónu '. $seas), callback($this, 'usersGridOperationsHandler'))
//		 ->setConfirm('deactivate', 'Určitě chcete vybráné členy de/aktivovat?')
//		 ->setConfirm('application', 'Určitě chcete vybráné členy přihlásit na nejbližší sezónu?');
//	if ($this->user->isAllowed("Admin:users", "update")) {
//	    $grid->addAction('edit', '[Edit]', Action::TYPE_HREF, 'editUser');
//	} else {
//	    if ($this->user->isAllowed("Admin:users", "view"))
//	    $grid->addAction('show', '[Zobraz] ', Action::TYPE_HREF, 'showUser');
//	}
	//$grid->addActionHref('application', '[Prihl]', 'userApplications');
	// setDisable() - nastavi callback, kdy ma byt vypnuto - vhodne pri overovani opravneni
	$grid->addActionHref("regenPassword", "", 'regenPassword!')
		//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("usersModule.admin.grid.pwRegen")]))
		->setIcon('lock')
		->setConfirm(function($u) {
		    return "Are you sure you want to regenerate password for user {$u->surname} {$u->name} ({$u->id})?";
		});
	$grid->addActionHref('delete', '', "deleteUser!")
		//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("usersModule.admin.grid.delete")]))
		->setIcon('trash')
		->setConfirm(function($u) {
		    return "Are you sure you want to delete user {$u->surname} {$u->name} ({$u->id})?";
		});
	$grid->addActionHref('update', '', 'updateUser')
		//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("usersModule.admin.grid.update")]))
		->setIcon('pencil');
	

	$grid->addActionHref('webProfile', '', 'updateWebProfile')
		//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("usersModule.admin.grid.updateWp")]))
		->setIcon('th-list');

	$operation = array('delete' => 'Delete', 'activeToggle' => 'ActiveToggle');
	$grid->setOperation($operation, $this->usersGridOperationsHandler)
		->setConfirm('delete', $this->tt("usersModule.admin.grid.reallyDeleteItems"));

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-users " . date("Y-m-d H:i:s", time()));

	return $grid;
    }

    /**
     * Users grid operations handler
     * @param string $operation name
     * @param string $id Array of identifier
     */
    public function usersGridOperationsHandler($operation, $id) {
	switch ($operation) {
	    case 'deactivate':
		foreach ($id as $i) {
		    $this->doToggleActivity($i);
		}
		break;
	    case 'delete':
		foreach ($id as $i) {
		    $this->doDeleteUser($id);
		}
		break;
	}
	$this->redirect('this');
    }

    // </editor-fold>
    // <editor-fold desc="WEB PROFILE CHECK">

    public function createComponentWebProfilesPermitGrid($name) {

	$grid = new Grid($this, $name);
	$grid->setModel($this->userService->getWebProfilesToPermitDatasource());

	$grid->translator->lang = $this->getLocale();
	$grid->setDefaultPerPage(30);
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText("personalLikes", "Content")
			->setTruncate(100)
			->setCustomRender($this->wpDataRender)
		->cellPrototype->class[] = 'center';

	$headerData = $grid->getColumn('personalLikes')->headerPrototype;
	$headerData->class[] = 'center';
	$headerData->rowspan = "2";
	$headerData->style['width'] = '40%';

	$grid->addActionHref('yes', '', "permitProfile!")
		//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("usersModule.admin.grid.permitProfile")]))
		->setIcon('ok')
		->setConfirm(function($u) {
		    return "Are you sure you really want to PERMIT profile #{$u->getId()}?";
		});

	$grid->addActionHref('no', '', "denyProfile!")
		//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("usersModule.admin.grid.denyProfile")]))
		->setIcon('remove')
		->setConfirm(function($u) {
		    return "Are you sure you really want to DENY profile #{$u->getId()}?";
		});

	$operation = array('yes' => 'Permit', 'no' => 'Deny');
	$grid->setOperation($operation, $this->wppGridOperationsHandler)
		->setConfirm("yes", $this->tt("usersModule.admin.wpGrid.reallyPermitItems"))
		->setConfirm("no", $this->tt("usersModule.admin.wpGrid.reallyDenyItems"));

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-users " . date("Y-m-d H:i:s", time()));

	return $grid;
    }

    public function wpDataRender($e) {
	$res = $e->getFavouriteBrand() . ", " .
		$e->getFavouriteClub() . ", " .
		$e->getEquipment() . ", " .
		$e->getHowGotThere() . ", " .
		$e->getPersonalDislikes() . ", " .
		$e->getPersonalLikes() . ", " .
		$e->getPersonalInterests() . ", " .
		$e->getSportExperience() . ", " .
		$e->getSignature();
	return \Nette\Utils\Html::el("span")->setText($res)->addAttributes(["title" => $res]);
    }

    public function handleDenyProfile($id) {
	$this->doDenyProfile($id);
	$this->redirect("this");
    }

    public function handlePermitProfile($id) {
	$this->doPermitProfile($id);
	$this->redirect("this");
    }

    public function wppGridOperationsHandler($operation, $id) {
	switch ($operation) {
	    case "yes":
		foreach ($id as $i) {
		    $this->doPermitProfile($id);
		}
		break;
	    case "no":
		foreach ($id as $i) {
		    $this->doDenyProfile($id);
		}
	}
	$this->redirect("this");
    }

    private function doPermitProfile($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id, "Admin:default");
	try {
	    $this->userService->permitWebProfile($id, $this->getUser()->getIdentity());
	} catch (Exceptions\InvalidArgumentException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.badArgumentFormat", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.webProfilePermitFailed", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
    }

    private function doDenyProfile($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id, "Admin:default");
	try {
	    $this->userService->denyWebProfile($id, $this->getUser()->getIdentity());
	} catch (Exceptions\InvalidArgumentException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.badArgumentFormat", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex->getMessage());
	    $m = $this->tt("usersModule.admin.webProfileDenyFailed", ["id" => $id]);
	    $this->flashMessage($m, self::FM_ERROR);
	}
    }
    // </editor-fold>
}
