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

use App\Model\Entities\SportGroup;

/**
 * Interface for Event service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IGalleryService {
    
    /**
     * 
     */
    function createGallery(Gallery $g);
    
    /**
     * 
     */
    function updateGallery(Gallery $g);
    
    /**
     * 
     */
    function deleteGallery(Gallery $g);
    
    /**
     * 
     */
    function getGallery($id);
    
    /**
     * 
     */
    function getGalleries(SportGroup $g);
    
    /**
     * 
     */
    function addMedia(Gallery $g, array $media);
    
    /**
     * 
     */
    function removeMedia(Gallery $g, array $media);
}
