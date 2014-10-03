<?php

/**
 * Syntax sugar for dump() function
 */
function dd() {
    dump(func_get_args());
}
//
//define('WWW_DIR', dirname(__FILE__));
//
//// absolute filesystem path to the cache storage
//define('CACHE_DIR', WWW_DIR . '/../temp/cache');
//
//// absolute filesystem path to the application root
//define('APP_DIR', WWW_DIR . '/../app');
//
//// absolute filesystem path to the application root
//define('LAYOUTS_DIR', APP_DIR . '/../templates');
//
//// absolute filesystem path to the libraries
//define('LIBS_DIR', WWW_DIR . '/../vendor/others');
//
//// absolute filesystem path to the images
//define ('IMAGES_DIR', WWW_DIR . '/images');

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getService('application')->run();
