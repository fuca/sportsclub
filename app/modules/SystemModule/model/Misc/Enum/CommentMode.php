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
 * Enumerate for representing Comment modes
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class CommentMode extends EnumType implements IEnum {
    const 
	ALLOWED	    = "yes",
	RESTRICTED  = "no",
	SIGNED	    = "sig",
	GROUP	    = "grp";
    
    protected $name = "CommentMode";
    protected $values = [self::ALLOWED, self::RESTRICTED, self::SIGNED, self::GROUP];
    
    public static function getOptions() {
	return array(
	    self::ALLOWED	=>  "systemModule.commentMode.allowed", 
	    self::RESTRICTED	=>  "systemModule.commentMode.restricted", 
	    self::SIGNED	=>  "systemModule.commentMode.signedIn", 
	    self::GROUP		=>  "systemModule.commentMode.group");
    }
}
