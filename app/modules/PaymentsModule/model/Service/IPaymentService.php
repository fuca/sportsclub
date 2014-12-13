<?php

/**
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

namespace App\PaymentsModule\Model\Service;

use \App\Model\Entities\Payment,
    \App\Model\Entities\User;

/**
 * Interface  for Payment Service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IPaymentService {
    
    /**
     * Persists payment into database
     */
    function createPayment(Payment $p);
    
    /**
     * Updates existing payment within database
     */
    function updatePayment(Payment $p);
    
    /**
     * Permanently removes passed Payment
     */
    function deletePayment($id);
    
    function getPayment($id);
    
    function getPaymentsDatasource(User $u = null);
    
    function generateVs(Payment $p);
    
    function getDefaultDueDate();
    
    function markAsDoneAcc($id, User $user);
    function markAsDoneCash($id, User $user);
    function markAsDoneSent($id, User $user);
    
}
