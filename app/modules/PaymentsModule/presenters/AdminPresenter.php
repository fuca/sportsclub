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

namespace App\PaymentsModule\Presenters;

use \Grido\Grid,
    \App\Model\Entities\Payment,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\SportGroup,
    \Nette\ArrayHash,
    \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\Services\Exceptions\DataErrorException,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\Model\Misc\Exceptions,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Enum\PaymentStatus,
    \App\Model\Misc\Enum\PaymentOwnerType,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\SystemModule\Model\Service\IPositionService,
    \App\PaymentsModule\Forms\PaymentForm,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * Payments module admin presenter
 * @Secured(resource="PaymentsAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {

    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    public $seasonService;

    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $usersService;

    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupsService;

    /**
     * @inject
     * @var \App\PaymentsModule\Model\Service\IPaymentService
     */
    public $paymentService;

    /**
     * @inject
     * @var \App\SecurityModule\Model\Service\IPositionService
     */
    public $positionService;

    public function getPositionService() {
	return $this->positionService;
    }

    public function getPaymentService() {
	return $this->paymentService;
    }

    public function getSeasonService() {
	return $this->seasonService;
    }

    public function getUsersService() {
	return $this->usersService;
    }

    public function getSportGroupsService() {
	return $this->sportGroupsService;
    }
    
    /**
     * @Secured(resource="default")
     */
    public function actionDefault() {
	// render grid
    }

    /**
     * @Secured(resource="createPayment")
     */
    public function actionCreatePayment() {
	// render form
    }

    /**
     * 
     * @param \Nette\ArrayHash $values
     */
    public function createPaymentHandle(ArrayHash $values) {
	$payment = new Payment((array) $values);
	try {
	    $payment->setEditor($this->getUser()->getIdentity());
	    $this->getPaymentService()->createPayment($payment);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }

    private function createMultiPaymentHandle(ArrayHash $values, array $ids) {
	foreach ($ids as $id) {
	    $payment = new Payment((array) $values);
	    $payment->setOwner($id);
	    try {
		$payment->setEditor($this->getUser()->getIdentity());
		$this->getPaymentService()->createPayment($payment);
	    } catch (Exceptions\DataErrorException $ex) {
		$this->handleDataSave($id, "this", $ex);
	    }
	}
	$this->redirect("default");
    }

    /**
     * @Secured(resource="updatePayment")
     */
    public function actionUpdatePayment($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	try {
	    $dbPayment = $this->getPaymentService()->getPayment($id, false);
	    if ($dbPayment !== null) {
		$form = $this->getComponent('updatePaymentForm');
		$form->setDefaults($dbPayment->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "this", $ex);
	}
    }

    public function updatePaymentHandle(ArrayHash $values) {
	$payment = new Payment((array) $values);
	try {
	    $payment->setEditor($this->getUser()->getIdentity());
	    $this->getPaymentService()->updatePayment($payment);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }

    /**
     * @Secured(resource="deletePayment")
     */
    public function handleDeletePayment($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	$this->doDeletePayment($id);
	$this->redirect("this");
    }
    
    private function doDeletePayment($id) {
	try {
	    $this->getPaymentService()->deletePayment($id);
	} catch (Exceptions\DependencyException $ex) {
	    $this->handleDependencyDelete($id, "this", $ex);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }
	    
    public function createComponentAddPaymentForm($name) {
	$form = $this->preparePaymentForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdatePaymentForm($name) {
	$form = $this->preparePaymentForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function preparePaymentForm($name) {
	$form = new PaymentForm($this, $name, $this->getTranslator());
	try {
	    $seasons = $this->getSeasonService()->getSelectSeasons();
	    $users = $this->getUsersService()->getSelectUsers();
	    $groups = $this->getSportGroupsService()->getSelectAllSportGroups();
	    $form->setSeasons($seasons);
	    $form->setUsers($users);
	    $form->setSportGroups($groups);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	return $form;
    }

    public function paymentFormSubmitHandle(Form $form) {
	$values = $form->getValues();
	switch ($form->getMode()) {
	    case FormMode::CREATE_MODE:
		$oSelId = $values[PaymentForm::PAYMENT_OWNER_TYPE_SELECT_ID];
		switch ($oSelId) {
		    case PaymentOwnerType::SINGLE:
			$this->createPaymentHandle($values);
			break;
		    case PaymentOwnerType::GROUP:
			$groupId = $values[PaymentForm::OWNER_TYPE_GROUP];
			try {
			    $users = $this->getPositionService()->getUsersWithinGroup($groupId);
			    $this->createMultiPaymentHandle($values, $users);
			} catch (Exceptions\DataErrorException $ex) {
			    $this->handleDataLoad(null, "this", $ex);
			}
			break;
		    case PaymentOwnerType::SELECT:
			$ids = $values[PaymentForm::OWNER_TYPE_SELECT];
			$this->createMultiPaymentHandle($values, $ids);
			break;
		}
		break;
	    case FormMode::UPDATE_MODE:
		$this->updatePaymentHandle($values);
		break;
	}
    }

    public function createComponentPaymentsGrid($name) {

	try {
	    $seasons = [null => null] + $this->seasonService->getSelectSeasons();
	    $users = [null => null] + $this->getUsersService()->getSelectUsers();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}

	$grid = new Grid($this, $name);
	$grid->setModel($this->getPaymentService()->getPaymentsDataSource());
	$grid->setPrimaryKey("id");

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('owner', $this->tt('paymentsModule.admin.grid.user'))
		->setSortable()
		->setFilterSelect($users);
	$headerLabel = $grid->getColumn('owner')->headerPrototype;
	$headerLabel->class[] = 'center';


	$grid->addColumnText('season', $this->tt('paymentsModule.admin.grid.season'))
		->setSortable()
		->setFilterSelect($seasons);
	$headerSeas = $grid->getColumn('season')->headerPrototype;
	$headerSeas->class[] = 'center';

	$grid->addColumnDate('dueDate', $this->tt('paymentsModule.admin.grid.dueDate'), self::DATE_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerOrdered = $grid->getColumn('dueDate')->headerPrototype;
	$headerOrdered->class[] = 'center';

	$grid->addColumnNumber('amount', $this->tt('paymentsModule.admin.grid.amount'))
		->setSortable()
		->setFilterNumber();
	$headerAm = $grid->getColumn('amount')->headerPrototype;
	$headerAm->class[] = 'center';

	$states = [null => null] + PaymentStatus::getOptions();
	$grid->addColumnText('status', $this->tt('paymentsModule.admin.grid.status'))
		->setTruncate(9)
		->setSortable()
		->setCustomRender($this->statusRender)
		->setFilterSelect($states);

	$headerSta = $grid->getColumn('status')->headerPrototype;
	$headerSta->class[] = 'center';

	$grid->addActionHref('delete', '', 'deletePayment!')
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("paymentsModule.admin.grid.messages.rlyDelPayment", null, ["id" => $u->getId()]);
		});

	$grid->addActionHref('edit', '', 'updatePayment')
		->setIcon('pencil');

	$grid->setOperation(["delete" => $this->tt("system.common.delete"),
		    "markCash" => $this->tt("paymentsModule.admin.grid.markDoneCash"),
		    "markAcc" => $this->tt("paymentsModule.admin.grid.markDoneAcc"),
		    "markSent" => $this->tt("paymentsModule.admin.grid.markSent")], $this->paymentsGridOpsHandler)
		->setConfirm("delete", $this->tt("paymentsModule.admin.grid.messages.rlyDelPaymentItems"));
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-payments " . date("Y-m-d H:i:s", time()));
    }
    
    public function statusRender($e) {
	return $this->tt(PaymentStatus::getOptions()[$e->getStatus()]);
    }

    public function paymentsGridOpsHandler($op, $ids) {
	$me = $this->getUser()->getIdentity();
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeletePayment($id);
		}
		break;
	    case "markCash":
		foreach ($ids as $id) {
		    try {
			$this->paymentService->markAsDoneCash($id, $me);
		    } catch (Exceptions\DataErrorException $ex) {
			$this->handleDataSave($id, "this", $ex);
		    }
		}
		break;
	    case "markAcc":
		foreach ($ids as $id) {
		    try {
			$this->paymentService->markAsDoneAcc($id, $me);
		    } catch (Exceptions\DataErrorException $ex) {
			$this->handleDataSave($id, "this", $ex);
		    }
		}
		break;
	    case "markSent":
		foreach ($ids as $id) {
		    $this->paymentService->markAsSent($id, $me);
		}
		break;
	}
	$this->redirect("this");
    }
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("paymentsModule.admin.paymentAdd", ":Payments:Admin:addPayment");
	$c->addNode("systemModule.navigation.back", ":System:Default:adminRoot");
	return $c;	
    }
    
    public function createComponentBackSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("systemModule.navigation.back",":Payments:Admin:default");
	return $c;
    }
}
