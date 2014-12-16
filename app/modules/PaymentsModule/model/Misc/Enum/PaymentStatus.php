<?php
/*
 * Copyright 2014 fuca.
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

namespace App\Model\Misc\Enum; 

use \App\Model\Misc\Enum\EnumType;

/**
 * Enumerate for representing payment status
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class PaymentStatus extends EnumType implements IEnum {

    const 
	NOT_YET		= "no",
	SENT		= "snt",
	YES_CASH	= "cash",
	YES_ACCOUNT	= "acc";
    
    protected $name = "PaymentStatus";
    protected $values = [self::NOT_YET, self::YES_ACCOUNT, self::YES_CASH, self::SENT];

     public static function getOptions() {
	return array(
	    self::NOT_YET	=>  "paymentsModule.paymentStatus.no",
	    self::SENT		=>  "paymentsModule.paymentStatus.sent",
	    self::YES_CASH	=>  "paymentsModule.paymentStatus.cash",
	    self::YES_ACCOUNT	=>  "paymentsModule.paymentStatus.acc");
    }
}