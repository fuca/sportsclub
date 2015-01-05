<?php

namespace App;

use \Nette,
    \Nette\Application\Routers\RouteList,
    \Nette\Application\Routers\Route,
    \Nette\Application\Routers\SimpleRouter;

/**
 * Router factory.
 */
class RouterFactory {

    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter() {
	$router = new RouteList();
	$router[] = new Route('index.php',"System:Homepage:default");
	$router[] = new Route('clanky[/<locale=cs cs|en>]',"Articles:Public:default");
	$router[] = new Route('kontakty[/<locale=cs cs|en>]',"Security:Public:default");
	$router[] = new Route('[<locale=cs cs|en>/]<presenter>/<action>[/<id>]', 'System:Homepage:default');
	
	return $router;
    }

}
