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

use App\Model\Entities\User;

/**
 * Interface of Notification service
 * contacting users about changes, dealing with mass emails
 * @author <michal.fuca.fucik(at)gmail.com>
 * @package sportsclub
 */
interface INotificationService {
    
    /**
     * Sends notification about creating new account
     * @param \App\Model\Entities\User $u
     */
    public function newRegistrationNotification(User $u);
    
    /**
     * Sends notification about creating new season application
     * @param \App\Model\Entities\User $u
     */
    public function newApplicationNotification(User $u);
    
    /**
     * Sends notification about account activation
     * @param \App\Model\Entities\User $u
     */
    public function activationNotification(User $u);
    
    /**
     * Sends notification about account deactivation
     * @param \App\Model\Entities\User $u
     */
    public function deactivationNotification(User $u);
}
