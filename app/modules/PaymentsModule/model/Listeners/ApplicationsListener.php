<?php

namespace App\PaymentsModule\Model\Listeners;
use 
    Nette\Object,
     Kdyby\Events\Subscriber,
     App\Model\Entities\SeasonApplication;
	
/**
 * Description of ApplicationsListener
 *
 * @author fuca
 */
class ApplicationsListener extends Object implements Subscriber {
    
    public function getSubscribedEvents() {
	return ["SeasonApplicationService::onCreate"];
    }
    
    public function onCreate(SeasonApplication $app) {
	// vytvorit novy payment, takze si asi injektnout servisu
	dd("ApplicationListener");
    }

}
