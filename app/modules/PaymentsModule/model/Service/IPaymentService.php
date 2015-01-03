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
 * Interface for Payment Service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IPaymentService {

    /**
     * Creates payment entry
     * @param Payment $p
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createPayment(Payment $p);

    /**
     * Updates existing payment within database
     * @param Payment $p
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updatePayment(Payment $p);

    /**
     * Permanently removes passed Payment
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DependencyException
     * @throws Exceptions\DataErrorException
     */
    function deletePayment($id);

    /**
     * Gets single payment entry
     * @param numeric $id
     * @param boolean $useCache
     * @return type
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getPayment($id, $useCache);

    /**
     * @param User $u
     * @return Doctrine
     */
    function getPaymentsDatasource(User $u = null);

    /**
     * @param Payment $p
     * @return string
     */
    function generateVs(Payment $p);

    /**
     * Returns dueDate according to configuration
     * @return DateTime
     */
    function getDefaultDueDate();

    /**
     * 
     * @param numeric $id
     * @param User $user
     * @throws Exceptions\InvalidArgumentException
     */
    function markAsDoneAcc($id, User $user);

    /**
     * 
     * @param numeric $id
     * @param User $user
     * @throws Exceptions\InvalidArgumentException
     */
    function markAsDoneCash($id, User $user);

    /**
     * 
     * @param numeric $id
     * @param User $user
     * @throws Exceptions\InvalidArgumentException
     */
    function markAsDoneSent($id, User $user);
}
