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

use App\Model\Service\INotificationService,
    \App\Model\Entities\User,
    App\SystemModule\Model\Service\NotificationMessage;

/**
 * Implementation of INotification service for dealing with email notifications
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class EmailNotificationService implements INotificationService {
    
    const 
	MAILER_TYPE_SEND    = "send",
	MAILER_TYPE_SMTP    = "smtp";
	    

    /**
     * Absolute path of dir with prepared mass emails
     * @var string
     */
    private $massEmailStoragePath;

    public function setMassEmailStoragePath($path) {
	if (!file_exists($path))
	    throw new \Nette\InvalidStateException("Mass email storage directory does not exist");
	$this->massEmailStoragePath = $path;
    }

    public function activationNotification(User $u) {
	// notification about activation user account
    }

    public function deactivationNotification(User $u) {
	// notification about deactivation user account
    }

    public function newApplicationNotification(User $u) {
	// notification about sing in into season
    }

    public function newRegistrationNotification(User $u) {
	// notification about registration, is it neccessary?
    }

    public function send(NotificationMessage $n, $mailerType = self::MAILER_TYPE_SEND, $opts = []) {
	if ($n === null)
	    throw new \App\Model\Misc\Exceptions\NullPointerException("Argument Notification was null", 0);
	if ($mailerType !== self::MAILER_TYPE_SEND && $mailerType !== self::MAILER_TYPE_SMTP)
	    throw new \App\Model\Misc\Exceptions\InvalidArgumentException("Argument mailerType has on of EmailNotificationService's constant types, '{$mailerType}' given", 1);
	if ($mailerType == self::MAILER_TYPE_SEND) {
	    $mailer = new \Nette\Mail\SendmailMailer();
	} else {
	    $mailer = new \Nette\Mail\SmtpMailer($opts);
	}
	try {
	    $mailer->send($n);    
	} catch (Nette\InvalidStateException $ex) {
	    // LOG
	    dd($ex);
	}
    }

}
