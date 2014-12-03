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

use \App\SystemModule\Presenters\SystemUserPresenter,
    \Grido\Grid,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\PaymentStatus;
/**
 * PaymentPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class UserPresenter extends SystemUserPresenter {
    
    /**
     * @inject
     * @var \App\PaymentsModule\Model\Service\IPaymentService
     */
    public $paymentService;
    
    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    public $seasonService;
    
    public function actionDefault() {
	// render grid
    }
    
    public function actionPaymentDetails($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$payment = null;
	try {
	    $payment = $this->paymentService->getPayment($id);
	    
	} catch (Exception $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$this->template->data = $payment;
    }
    
    public function handleMarkAsSent($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $this->paymentService->markAsDoneSent($id, $this->getUser()->getIdentity());
	} catch (Exception $ex) {
	    $this->handleDataSave($id, "this", $ex);
	}
	$this->redirect("this");
    }
    
    
    public function createComponentUserPaymentsGrid($name) {
	try {
	    $seasons = $this->seasonService->getSelectSeasons();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, ":System:Default:userRoot", $ex);
	}
	$grid = new Grid($this, $name);
	$grid->setModel($this->paymentService->getPaymentsDataSource($this->getUser()->getIdentity()));
	$grid->setPrimaryKey("id");

	
	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';
	
	$grid->addColumnNumber('amount', $this->tt('paymentsModule.admin.grid.amount'))
		->setSortable()
		->setFilterNumber();
	$headerAm = $grid->getColumn('amount')->headerPrototype;
	$headerAm->class[] = 'center';

	$grid->addColumnText('subject', $this->tt('paymentsModule.admin.grid.subject'))
		->setCustomRender($this->subjectRender)
		->setSortable()
		->setFilterText();
	
	$headerSeas = $grid->getColumn('subject')->headerPrototype;
	$headerSeas->class[] = 'center';
	
	$grid->addColumnDate('dueDate', $this->tt('paymentsModule.admin.grid.dueDate'), self::DATE_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerOrdered = $grid->getColumn('dueDate')->headerPrototype;
	$headerOrdered->class[] = 'center';
	
	$states = [null => null] + PaymentStatus::getOptions();
	$grid->addColumnText('status', $this->tt('paymentsModule.admin.grid.status'))
		->setTruncate(9)
		->setSortable()
		->setReplacement($states)
		->setFilterSelect($states);
	
	$grid->addColumnText('season', $this->tt('paymentsModule.admin.grid.season'))
		->setSortable()
		->setFilterSelect($seasons);
	$headerSeas = $grid->getColumn('season')->headerPrototype;
	$headerSeas->class[] = 'center';

	$headerSta = $grid->getColumn('status')->headerPrototype;
	$headerSta->class[] = 'center';
	
	$grid->addActionHref("show", "", "paymentDetails")
		->setIcon("eye-open");
	$grid->addActionHref("markAsSent", "", "markAsSent!")
		->setIcon("check")
		->setConfirm(function($u) {
		    return $this->tt("paymentsModule.protected.grid.messages.rlyMarkAsSent", null, ["id" => $u->getId()]);
		});
	$grid->setFilterRenderType($this->filterRenderType);
	return $grid;
    }
    
    public function subjectRender($e) {
	return \Nette\Utils\Html::el("span")
		->addAttributes(["title"=>$e->getSubject()])
		->setText(\Nette\Utils\Strings::truncate($e->getSubject(), 20));

    }
    
}
