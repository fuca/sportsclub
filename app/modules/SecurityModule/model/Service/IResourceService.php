<?php


namespace App\SecurityModule\Model\Service;

/**
 *
 * @author fuca
 */
interface IResourceService {
    
    function getResources();
    function getResource($id);
    //function getPrivileges(Resource $r);
    function getSelectResources();
}
