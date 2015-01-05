

Installing
----------

The best way to install Nette Framework is to download latest package
from http://nette.org/download or create new project using Composer:

1. Install Composer: (see http://getcomposer.org/download)

		curl -s http://getcomposer.org/installer | php

2. Create new project via Composer:

		php composer.phar create-project nette/sandbox myApplication
		cd myApplication

Make directories `temp` and `log` writable. Navigate your browser
to the `www` directory and you will see a welcome page. PHP 5.4 allows
you run `php -S localhost:8888 -t www` to start the web server and
then visit `http://localhost:8888` in your browser.


It is CRITICAL that file `app/config/config.neon` & whole `app`, `log`
and `temp` directory are NOT accessible directly via a web browser! If you
don't protect this directory from direct web access, anybody will be able to see
your sensitive data. See [security warning](http://nette.org/security-warning).




APACHE
    instalace
    nastaveni

adresare ktery musi mit nastaveny spesl prava

jak zachazet s databazi

jak pouzivat skripty

je potreba composer, apache, php, prohlizec

kde aplikace visi