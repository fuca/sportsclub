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

namespace App\Model\Misc\Exceptions;

/**
 * Description of DuplicateEntryException
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class DuplicateEntryException extends \Exception implements IException {
    
    const EMAIL_EXISTS = 101,
	  BIRTH_NUM_EXISTS = 102,
	  SEASON_LABEL = 103,
	  SEASON_TAX = 104,
	  SEASON_APPLICATION = 105;

    public function __construct($message = null, $code = 0, \Exception $previous = NULL) {
	parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
	return "### PRESENTERS LAYER ### - ". parent::__toString();
    }
}
