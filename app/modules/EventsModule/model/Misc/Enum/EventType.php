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
 * Enumerate for representing Event types
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class EventType extends EnumType implements IEnum {
    const 
	TRAINING	= "trn",
	MATCH		= "mt",
	LEAGUE_MATCH	= "lmt",
	TEAMBUILDING	= "teb",
	MEETING		= "met";
    
    protected $name = "EventType";
    protected $values = [self::TRAINING, self::MATCH, self::LEAGUE_MATCH, self::TEAMBUILDING, self::MEETING];

    public static function getOptions() {
	return array(
	    self::TRAINING	=>  "Trénink",
	    self::MATCH		=>  "Zápas",
	    self::LEAGUE_MATCH	=>  "Ligový zápas",
	    self::TEAMBUILDING	=>  "Teambuilding",
	    self::MEETING	=>  "Schůze");
    }
}
