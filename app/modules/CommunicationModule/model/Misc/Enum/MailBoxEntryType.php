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
 * Enumerate for representing Article status
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class MailBoxEntryType extends EnumType implements IEnum {
    
    const 
	UNREAD	= "unr",
	READ	= "red",
	DELETED	= "del";
    
    protected $name = "MailBoxEntryType";
    protected $values = [self::READ, self::UNREAD, self::DELETED];
    
    public static function getOptions() {
	return array( 
	    self::READ		=>  "Přečtené", 
	    self::UNREAD	=>  "Nepřečtené",
	    self::DELETED	=>  "Smazané");
    }
}
