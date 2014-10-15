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
    \App\SystemModule\Presenters\SecuredPresenter,
    \App\Services\Exceptions\DataErrorException,
    Kdyby\Doctrine\DuplicateEntryException,
    \App\Services\Exceptions,
    \Nette\Application\UI\Form,
    \App\Model\Misc\PaymentOwnerType,
    \App\SystemModule\Model\Service\ISportGroupService,
    App\SystemModule\Model\Service\IPositionService,
    App\PaymentsModule\Forms\PaymentForm;

/**
 * Payments module admin presenter
 * @Secured resource={payments.admin}
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    public $seasonService;

    /**
     * @inject
     * @var \App\Model\Service\IUserService
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
     * @var \App\Model\Service\IPositionService
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

    public function actionCreatePayment() {
	// render form
    }

    public function createPaymentHandle(ArrayHash $values) {
	$payment = new Payment((array) $values);
	try {
	    // TODO set editor and OTHER STUFF
	    $this->getPaymentService()->createPayment($payment);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Payment could not be created", self::FM_ERROR);
	}
	$this->redirect("default");
    }

    private function createMultiPaymentHandle($values, array $ids) {
	foreach ($ids as $id) {
	    $payment = new Payment((array) $values);
	    $payment->setOwner($id);
	    try {
		// TODO set editor and OTHER STUFF
		$this->getPaymentService()->createPayment($payment);
	    } catch (DataErrorException $ex) {
		$this->flashMessage("Payment could not be created", self::FM_ERROR);
	    }
	}
	$this->redirect("default");
    }

    public function actionUpdatePayment($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $dbPayment = $this->getPaymentService()->getPayment($id, false);
	    if ($dbPayment !== null) {
		$form = $this->getComponent('updatePaymentForm');
		$form->setDefaults($dbPayment->toArray());
	    }
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se načíst požadovaná data", self::FM_ERROR);
	}
    }

    public function updatePaymentHandle(ArrayHash $values) {
	$payment = new Payment((array) $values);
	try {
	    // TODO set editor and OTHER STUFF
	    $this->getPaymentService()->updatePayment($payment);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se uložit požadované změny", self::FM_ERROR);
	}
	$this->redirect("default");
    }

    public function handleDeletePayment($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát arugmentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->getPaymentService()->deletePayment($id);
	} catch (DataErrorException $ex) {
	    switch ($ex->getCode()) {
		case 1000:
		    $this->flashMessage("Nemůžete smazat platbu, která je užívána jinými entitami systému", self::FM_ERROR);
		    break;
	    }
	} catch (Exception $ex) {
	    dd($ex);
	}
	$this->redirect("this");
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
	    $groups = $this->getSportGroupsService()->getSelectSportGroups();
	    $form->setSeasons($seasons);
	    $form->setUsers($users);
	    $form->setSportGroups($groups);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se načíst potřebná data", self::FM_ERROR);
	    dd($ex);
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
			} catch (DataErrorException $ex) {
			    $this->flashMessage("Nepodařilo se načíst potřebná data", self::FM_ERROR);
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
	$grid = new Grid($this, $name);
	$grid->setModel($this->getPaymentService()->getPaymentsDataSource());
	$grid->setPrimaryKey("id");

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('owner', 'Člen')
		->setSortable();
	$headerLabel = $grid->getColumn('owner')->headerPrototype;
	$headerLabel->class[] = 'center';

	$grid->addColumnText('season', 'Sezóna')
		->setSortable();
	$headerLabel = $grid->getColumn('season')->headerPrototype;
	$headerLabel->class[] = 'center';

	$grid->addColumnDate('orderedDate', 'Zadáno', self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerAuthor = $grid->getColumn('orderedDate')->headerPrototype;
	$headerAuthor->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deletePayment!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updatePayment')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-payments " . date("Y-m-d H:i:s", time()));
    }

}
