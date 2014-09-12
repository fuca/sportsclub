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

use App\Model\Entities\User,
    App\Model\Entities\PrivateMessage;

/**
 * Interface for IMessage service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IMessageService {
    
    /**
     * 
     */
    function sendMessage(PrivateMessage $m);
    
    /**
     * 
     */
    function deleteMessage(PrivateMessage $m);
    
    /**
     * 
     */
    function getMessage(user $u);
    
    /**
     * 
     */
    function getIncoming(User $u);
    
    /**
     * 
     */
    function getOutcoming(User $u);
    
    /**
     * 
     */
    function getDeleted(User $u);
    
    /**
     * 
     */
    function toggleStar(PrivateMessage $m, User $u);
    
}
