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

namespace App\SystemModule\Model\Service;

use \App\SystemModule\Model\Service\INotificationService,
    \App\Model\Entities\User,
    \Nette\Mail\Message,
    \Kdyby\Monolog\Logger,
    \App\Model\Misc\Exceptions,
    \Kdyby\Translation\Translator,
    \Nette\Mail\SendmailMailer,
    \Nette\Mail\SmtpMailer,
    \App\Model\Entities\SeasonApplication,
    \App\Model\Entities\MailBoxEntry,
    \App\Model\Entities\Payment;

/**
 * Implementation of INotification service for dealing with email notifications
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class EmailNotificationService implements INotificationService {

    const
	    MAILER_TYPE_SEND = "send",
	    MAILER_TYPE_SMTP = "smtp";
    
    /**
     * @var string
     */
    private $desiredMailerType;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * @var \Kdyby\Translation\Translator
     */
    private $translator;
    
    /**
     * @var string
     */
    private $senderEmail;
    
    /**
     * Name of club
     * @var string
     */
    private $hostName;
    
    /**
     * Options for send mail thru smtp
     * @var array
     */
    private $smtpOptions;
    
    public function __construct(Logger $logger, Translator $translator) {
	$this->logger = $logger;
	$this->translator = $translator;
    }
    
    public function getSenderEmail() {
	if (!isset($this->senderEmail))
	    throw new Exceptions\InvalidStateException("Property senderEmail is not set, use appropriate setter first");
	return $this->senderEmail;
    }

    public function setSenderEmail($senderEmail) {
	$this->senderEmail = $senderEmail;
    }
    
    public function getHostName() {
	if (!isset($this->hostName))
	    throw new Exceptions\InvalidStateException("Property hostName is not set, use appropriate setter first");
	return $this->hostName;
    }

    public function setHostName($hostName) {
	$this->hostName = $hostName;
    }
    
    public function getSmtpOptions() {
	if (!isset($this->smtpOptions) || 
	    !is_array($this->smtpOptions))
	    throw new Exceptions\InvalidStateException("Property smtpOptions is not set, use appropriate setter first");
	return $this->smtpOptions;
    }

    /**
     * @param array $smtpOptions
     */
    public function setSmtpOptions($smtpOptions) {
	$this->smtpOptions = $smtpOptions;
    }
    
    public function getDesiredMailerType() {
	if  ($this->desiredMailerType !== self::MAILER_TYPE_SEND && 
		$this->desiredMailerType !== self::MAILER_TYPE_SMTP)
	    throw new Exceptions\InvalidStateException("Property smtpOptions is not set to correct value.");
	return $this->desiredMailerType;
    }

    public function setDesiredMailerType($desiredMailerType) {
	$this->desiredMailerType = $desiredMailerType;
    }
    
    public function notifyAccountActivated(User $u) {
	$subjKey = "systemModule.notification.accActivated.subject";
	$bodyKey = "systemModule.notification.accActivated.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    public function notifyAccountDeactivated(User $u) {
	$subjKey = "systemModule.notification.accDeactivated.subject";
	$bodyKey = "systemModule.notification.accDeactivated.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    public function notifyNewAccount(User $u) {
	$subjKey = "systemModule.notification.accCreated.subject";
	$bodyKey = "systemModule.notification.accCreated.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname(),
		"pass"=>$u->provideRawPassword()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    public function notifyNewMessage(MailBoxEntry $e) {
	$u = $e->getRecipient();
	$subjKey = "systemModule.notification.newMessage.subject";
	$bodyKey = "systemModule.notification.newMessage.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    public function notifyNewPayment(Payment $p) {
	$u = $p->getOwner();
	$subjKey = "systemModule.notification.newPayment.subject";
	$bodyKey = "systemModule.notification.newPayment.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    public function notifyNewSeasonApplication(SeasonApplication $app) {
	$u = $app->getOwner();
	$subjKey = "systemModule.notification.newApplication.subject";
	$bodyKey = "systemModule.notification.newApplication.body";
	$subject = $this->translator
		->translate($subjKey, null, [
		    "host"=>$this->getHostName(), 
		    "season"=>$app->getSeason()->getLabel(), 
		    "group"=>$app->getSportGroup()->getName()." ({$app->getSportGroup()->getSportType()->getName()})"]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    public function notifyPasswordChange(User $u) {
	$subjKey = "systemModule.notification.passwordChange.subject";
	$bodyKey = "systemModule.notification.passwordChange.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname(),
		"pass"=>$u->provideRawPassword()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }
    
    public function notifyNewPassword(User $u) {
	$subjKey = "systemModule.notification.newPassword.subject";
	$bodyKey = "systemModule.notification.newPassword.body";
	$subject = $this->translator->translate($subjKey, null, ["host"=>$this->getHostName()]);
	$body = $this->translator->translate($bodyKey, null, 
		["name"=>$u->getName(),
		"surname"=>$u->getSurname(),
		"pass"=>$u->provideRawPassword()]);
	
	$mail = new Message();
	$mail->setFrom($this->getSenderEmail())
		->setSubject($subject)
		->setBody($body)
		->addTo($u->getContact()->getEmail());
	
	$this->send($mail);
    }

    private function send(Message $n) {
	$mailerType = $this->getDesiredMailerType();
	$mailer = null;
	
	if ($mailerType == self::MAILER_TYPE_SEND) {
	    $mailer = new SendmailMailer();
	} else {
	    $mailer = new SmtpMailer($this->getSmtpOptions());
	}
	
	try {
	    $mailer->send($n);
	} catch (Exceptions\InvalidStateException $ex) {
	    $this->logger->addError($ex);
	}
    }

}
