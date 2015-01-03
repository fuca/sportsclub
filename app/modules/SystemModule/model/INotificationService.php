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

use \App\Model\Entities\User,
    \App\Model\Entities\Payment,
    \App\Model\Entities\MailBoxEntry,
    \App\Model\Entities\SeasonApplication;

/**
 * Interface of Notification service
 * contacting users about changes, dealing with mass emails
 * @author <michal.fuca.fucik(at)gmail.com>
 * 
 * @package sportsclub
 */
interface INotificationService {
    
    /**
     * Sends notification about creating of new password
     * @param User $u
     */
    function notifyNewPassword(User $u);
    
    /**
     * Sends notification about creating new account 
     * @param User $u
     */
    function notifyNewAccount(User $u);
    
    /**
     * Sends notification about deactivation of User's account
     * @param User $u
     */
    function notifyAccountDeactivated(User $u);
    
    /**
     * Sends notification about user's account activation
     * @param User $u
     */
    function notifyAccountActivated(User $u);
    
    /**
     * Sends notification about creating new payment
     * @param Payment $u
     */
    function notifyNewPayment(Payment $u);
    
    /**
     * Sends notification about receving new message
     * @param MailBoxEntry $entry
     */
    function notifyNewMessage(MailBoxEntry $entry);
    
    /**
     * Sends notification about creating new seasonApplication
     * @param SeasonApplication $app
     */
    function notifyNewSeasonApplication(SeasonApplication $app);
    
    /**
     * Sends notification about regeneration of system password
     * @param User $u
     */
    function notifyPasswordChange(User $u);
}
