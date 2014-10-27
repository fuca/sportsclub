<?php

namespace App\PaymentsModule\Model\Listeners;

use \Nette\Object,
    \Nette\Utils\DateTime,
    \Kdyby\Events\Subscriber,
    \App\Model\Entities\SeasonApplication,
    \Kdyby\Monolog\Logger,
    \App\SeasonsModule\Model\Service\ISeasonApplicationService,
    \App\SeasonsModule\Model\Service\ISeasonTaxService,
    \App\PaymentsModule\Model\Service\IPaymentService,
    \App\Model\Misc\Enum\PaymentStatus,
    \App\Model\Entities\Payment;
	
	
/**
 * Description of ApplicationsListener
 *
 * @author fuca
 */
class ApplicationsListener extends Object implements Subscriber {
    
    /**
     * @return \App\PaymentsModule\Model\Service\IPaymentService
     */
    private $paymentService;
    
    /**
     * @return \App\SeasonsModule\Model\Service\ISeasonTaxService
     */
    private $seasonTaxService;
    
    /**
     * @return \App\SeasonsModule\Model\Service\ISeasonApplicationService
     */
    private $seasonApplicationService;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;

    public function setPaymentService(IPaymentService $paymentService) {
	$this->paymentService = $paymentService;
    }

    public function setSeasonTaxService(ISeasonTaxService $seasonTaxService) {
	$this->seasonTaxService = $seasonTaxService;
    }

    public function setSeasonApplicationService(ISeasonApplicationService $seasonApplicationService) {
	$this->seasonApplicationService = $seasonApplicationService;
    }
        
    public function getSubscribedEvents() {
	return ["App\SeasonsModule\Model\Service\SeasonApplicationService::onCreate"];
    }
    
    public function __construct(Logger $logger) {
	$this->logger = $logger;
    }
    
    public function onCreate(SeasonApplication $app) {
	$amount = null;
	$season = $app->getSeason();
	$group = $app->getSportGroup();
	try {
	    $tax = $this->seasonTaxService->getSeasonTaxSG($season, $group);
	    if ($tax !== null) {
		$amount = $tax->getMemberShip();
		if (empty($amount) || $amount == 0) return;
	    } else {
		throw new Exceptions\InvalidStateException("Season tax for season $season and group $group does not exist");
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logger->addError("Application listener - onCreate - getSeasonTaxSG failed with - ". $ex->getMessage());
	    return;
	}
	
	$subject = "Application for ".$app->getSportGroup()->getName()
		." (".$app->getSportGroup()->getSportType()->getName()
		.") within ". $app->getSeason()->getLabel()." season";
	
	$payment = new Payment();
	$payment->setOwner($app->getOwner());
	$payment->setSeason($app->getSeason());
	$payment->setSubject($subject);
	$payment->setAmount($amount);
	$payment->setDueDate($this->paymentService->getDefaultDueDate());
	$payment->setOrderedDate(new DateTime());
	$payment->setEditor($app->getEditor());
	$payment->setStatus(PaymentStatus::NOT_YET);
	$payment->setVs($this->paymentService->generateVs($payment));
	$payment->setPublicNote("");
	$payment->setProtectedNote("");
	
	try {
	    $this->paymentService->createPayment($payment);
	    $app->setPayment($payment);
	    $this->seasonApplicationService->updateSeasonApplication($app);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logger->addError("Application listener - onCreate - savingData failed with - ". $ex->getMessage());
	    return;
	}
    }

}
