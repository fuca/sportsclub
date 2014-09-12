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

use App\SystemModule\Presenters\SecuredPresenter,
    App\UsersModule\Forms\UserForm,
    \Grido\Grid,
    \Nette\Utils\Strings,
    \Grido\Components\Actions\Action,
    \Grido\Components\Filters\Filter,
    \Grido\Components\Columns\Column,
    \Grido\Components\Columns\Date,
    \Nette\Mail\SendmailMailer,
    App\Misc\Passwords,
    App\Model\Entities\User,
    \App\Model\Entities\WebProfile,
    App\Model\Entities\Address,
    App\Model\Entities\Contact,
    \Nette\Mail\Message,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Exceptions\DataErrorException,
    \App\Model\Misc\Exceptions\DuplicateEntryException,
    \App\Model\Misc\Enum\FormMode,
    \Nette\DateTime,
    \Nette\ArrayHash;

/**
 * UserPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var App\Model\Service\IUserService
     */
    public $userService;

    /**
     * User admin presenter action of Default request
     */
    public function actionDefault() {
	// vykreslujeme grid
    }

    /////////////////////////// NEW USER ///////////////////////////////////////

    /**
     * User admin presenter action of New user request
     */
    public function actionNewUser() {
	// vykreslujeme formular
    }

    /**
     * Component factory for lazy initialize of newUserForm
     * @param string $name
     * @return \App\UsersModule\Forms\UserForm
     */
    public function createComponentNewUserForm($name) {
	$form = new UserForm($this, $name);
	$form->initialize();
	return $form;
    }

    /**
     * Create new user handler (topdown)
     * @param \Nette\ArrayHash $values
     * @throws DuplicateEntryException
     */
    public function createUser(ArrayHash $values) {

	$newPassword = Strings::random();
	// SEND NOTIFICATION
//	$mailer = new SendmailMailer();
//	$mail = new Message();
//	$mail->setFrom("sportsclub@gmail.com");
//	$mail->setSubject("New registration");
//	$mail->setContentType('text/html');
//	$mail->setBody("Vazeny uzivateli, byl vam vytvoren ucet v nasem sportovniho klubu XY\n\n Heslo: $newPassword a login vas email");
//	$mail->addTo("misan.128@seznam.cz");
//	$mailer->send($mail);

	$hashedPassword = Passwords::hash($newPassword, ['salt' => $this->getSalt()]);
	$nu = $this->hydrateUserFromUserForm($values);
	$nu->setPassword($hashedPassword);
	$nu->setWebProfile(new WebProfile());
	//$nu->getWebProfile()->setEditor($this->getUser);

	try {
	    $this->userService->createUser($nu);
	} catch (DataErrorException $e) {
	    switch ($e->getCode()) {
		case 21:
		case 22:
		    throw new DuplicateEntryException($e->getMessage(), $e->getCode(), $e);
	    }
	}
	$this->flashMessage("User {$nu->name} {$nu->surname} was successfully created with id {$nu->getId()}", self::FM_SUCCESS);
	$this->redirect("Admin:default");
    }

    //------------------------ END NEW USER ----------------------------------//
    ///////////////////////// REMOVE USER //////////////////////////////////////

    /**
     * Delete user handler (topdown)
     * @param numeric $id
     */
    public function handleDeleteUser($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Bad format of user id", self::FM_ERROR);
	    // TODO LOG SECURITY VIOLATION
	    return;
	}

	try {
	    $this->userService->deleteUser($id);
	} catch (Exceptions\DependencyException $e) {
	    $this->flashMessage("Nemůžete smazat entitu která figuruje v rámci jiných entit v systému", self::FM_ERROR);
	} catch(\Exception $e) {
	    dd($e);
	}
    }

    // ------------------------ END REMOVE USER ------------------------------//
    /////////////////////////// UPDATE USER ////////////////////////////////////

    /**
     * Action for filling updateUserForm control by values from database
     * @param numeric $id
     */
    public function actionUpdateUser($id) {
	if ($id === null) {
	    $this->flashMessage("Given user id was null", "error");
	    // TODO SECURITY VILOATION LOG
	    return;
	}
	if (!is_numeric($id)) {
	    $this->flashMessage("Given user id must be type of numeric", "error");
	    // TODO SECURITY VILOATION LOG
	    return;
	}

	$uUser = $this->userService->getUserId($id);
	$form = $this->getComponent('updateUserForm');

	$data = $uUser->toArray() + $uUser->getContact()->toArray() + $uUser->getContact()->getAddress()->toArray();
	$form->setDefaults($data);
    }

    /**
     * Hydrate User entity from UserForm
     * @param \Nette\ArrayHash $values
     * @return \App\Model\Entities\User
     */
    private function hydrateUserFromUserForm(ArrayHash $values) {
	$nu = new User();
	$nu->fromArray((array) $values);

	$na = new Address();
	$na->fromArray((array) $values);

	$nc = new Contact();
	$nc->fromArray((array) $values);

	$nc->setAddress($na);
	$nu->setContact($nc);
	return $nu;
    }

    /**
     * Update user handler (topdown)
     * @param \Nette\ArrayHash $values
     * @throws DuplicateEntryException
     */
    public function updateUser(ArrayHash $values) {
	try {
	    $this->userService->updateUser($this->hydrateUserFromUserForm($values));
	} catch (DataErrorException $e) {
	    switch ($e->getCode()) {
		case 21:
		case 22:
		    throw new DuplicateEntryException($e->getMessage(), $e->getCode(), $e);
	    }
	}
	$this->redirect("Admin:default");
    }

    /**
     * Component factory for lazy initialize of updateUserForm
     * @param string $name
     * @return \App\UsersModule\Forms\UserForm
     */
    public function createComponentUpdateUserForm($name) {
	$form = new UserForm($this, $name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    // ------------------------ END UPDATE USER ------------------------------//

    /**
     * Component factory of UsersGrid
     * @param string $name
     * @return \Grido\Grid
     */
    public function createComponentUsersGrid($name) {

	$grid = new Grid($this, $name);
	$grid->setModel($this->userService->getUsersDatasource());

//	$grid->setEditableColumns(function($id, $newValue, $oldValue, $column) {
////            dd($id);
////	    dd($newValue);
////	    dd($oldValue);
////	    dd($column);
//            return TRUE;
//        });
	
	$grid->translator->lang = 'cs';
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
		->setFilterText()
		->setSuggestion();
	$grid->getColumn('surname')->getEditableControl()->setRequired('Surname is required.');
	$headerSurname = $grid->getColumn('surname')->headerPrototype;
	$headerSurname->class[] = 'center';

	$grid->addColumnText('name', 'Jméno')
		->setSortable()
		->setFilterText()
		->setSuggestion();
	$grid->getColumn('name')->getEditableControl()->setRequired('Name is required.');
	$headerName = $grid->getColumn('name')->headerPrototype;
	$headerName->class[] = 'center';

//	$grid->addFilterCustom('nameOrSurname', new \Nette\Forms\Controls\TextArea('Jméno nebo příjmení'))
//		->setColumn('name')
//		->setColumn('surname', \Grido\Components\Filters\Condition::OPERATOR_OR)
//		->setCondition('LIKE ?')
//		->setFormatValue('%%value%');

	//$grid->getColumn('name')->getCellPrototype()->class('textleft');

	$activeList = [true=>'Ano', null=>'Ne'];
	$grid->addColumnNumber('active', 'Aktivní')
		->setReplacement($activeList)
		->setSortable();
	   // ->setEditableControl(new \Nette\Forms\Controls\SelectBox(NULL, $activeList));
	$headerActive = $grid->getColumn('active')->headerPrototype;
	$headerActive->class[] = 'center';

	$grid->addColumnDate('lastLogin', 'Poslední přihlášení')
		->setSortable()
		->setReplacement(array(NULL => 'Nikdy'))
		->setDateFormat(\Grido\Components\Columns\Date::FORMAT_DATETIME)
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
	$grid->addActionHref('application', '[Uprav]', 'updateUser')
		->setIcon('pencil');
	// setDisable() - nastavi callback, kdy ma byt vypnuto - vhodne pri overovani opravneni
	$grid->addActionHref('delete', '[Smaz]', "deleteUser!")
		->setIcon('trash')
		->setConfirm(function($u) {
		    return "Are you sure you want to delete user {$u->id} {$u->name} {$u->surname}?";
		});
//
	$operation = array('print' => 'Print', 'delete' => 'Delete', 'activeToggle' => 'ActiveToggle');
        $grid->setOperation($operation, $this->gridOperationsHandler)
            ->setConfirm('delete', 'Are you sure you want to delete %i items?');
		
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-users " . date("Y-m-d H:i:s", time()));
	
	return $grid;
    }

    /**
     * Grid operations handler
     * @param string $operation name
     * @param string $id Array of identifier
     */
    public function gridOperationsHandler($operation, $id) {
	switch ($operation) {
	    case 'deactivate':
		foreach ($id as $i) {
		    //$this->toggleActivity($i);
		    dd("Not implemented yet");
		}
		break;
	    case 'application':
		foreach ($id as $i) {
		    //$this->_semiAutomaticApplication($i);
		}
		dd("Not implemented yet");
		$this->redirect('this');
		break;
	    case 'delete':
		foreach ($id as $i) {
		    dd("Not implemented yet");
		}
		$this->redirect('this');
		break;
	}
    }

}
