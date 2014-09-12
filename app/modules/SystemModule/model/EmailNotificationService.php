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

namespace App\Model\Service;

use App\Model\Service\INotificationService,
	\App\Model\Entities\User;
/**
 * Implementation of INotification service for dealing with email notifications
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class EmailNotificationService implements INotificationService {
    
    /**
     * Absolute path of dir with prepared mass emails
     * @var string
     */
    private $massEmailStoragePath;
    
    // NEJAK SEM DOSTAT KONFIGURACI
    // potrebujeme konfigurovat nazev filu pro ruzny masovy notifikace
    // coz je dost pasivni, co kdyz si admin bude chtit udelat novou notifikaci
    // tak budeme muset rozlisovat jestli jde jen o nejaky lidi nebo o hromadnou notifikaci
    
    public function setMassEmailStoragePath($path) {
	if (!file_exists($path))
	    throw new \Nette\InvalidStateException("Mass email storage directory does not exist");
	$this->massEmailStoragePath = $path;
    }
    
    public function activationNotification(User $u) {
	
    }

    public function deactivationNotification(User $u) {
	
    }

    public function newApplicationNotification(User $u) {
	
    }

    public function newRegistrationNotification(User $u) {
	
    }

}
