<?php

/**
 * Syntax sugar for dump() function
 */
function dd() {
    dump(func_get_args());
}

//// absolute filesystem path to the application root
//define('TEMP_DIR', dirname(__FILE__) . '/../temp'); // due to error from presenterTree

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getService('application')->run();
