<?php

namespace App\UsersModule\Model\Misc\Utils;

use \App\Model\Entities\Address,
    \App\Model\Entities\Contact,
    \App\Model\Entities\User,
    \App\Model\Entities\WebProfile,
    \Nette\Utils\ArrayHash;

/**
 * Description of UserEntityManageHelper
 *
 * @author fuca
 */
class UserEntityManageHelper {

    /**
     * Hydrate User entity from UserForm
     * @param \Nette\ArrayHash $values
     * @return \App\Model\Entities\User
     */
    public static function hydrateUserFromHash(ArrayHash $values) {
	$nu = new User();
	$nu->fromArray((array) $values);

	$na = new Address();
	$na->fromArray((array) $values);

	$nc = new Contact();
	$nc->fromArray((array) $values);

	$nc->setAddress($na);
	$nu->setContact($nc);
	return $nu;
    }

}
