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
    
    function notifyNewPassword(User $u);
    function notifyNewAccount(User $u);
    function notifyAccountDeactivated(User $u);
    function notifyAccountActivated(User $u);
    function notifyNewPayment(Payment $u);    
    function notifyNewMessage(MailBoxEntry $entry);
    function notifyNewSeasonApplication(SeasonApplication $app);
    function notifyPasswordChange(User $u);
}
