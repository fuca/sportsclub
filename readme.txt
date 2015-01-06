================================================================================
			    MASARYKOVA UNIVERZITA
			     Fakulta informatiky
				  Brno 2014
		     Informační systém pro sportovní kluby
			      [Diplomová práce]
			      Bc. Michal Fučík
		       <michal.fuca.fucik(at)gmail.com>

				 readme.txt
--------------------------------------------------------------------------------


OBSAH
================================================================================

1. Úvod
2. Požadavky na provoz
3. Popis adresářové struktury projektu
4. Instrukce pro nasazení
5. Nette presenter
6. Konfigurace aplikace
7. Licence
8. Upozornění

--------------------------------------------------------------------------------



1) ÚVOD
================================================================================

Tento soubor je doplňkem k textové části diplomové práce "Informační systém pro 
sportovní kluby". Obsah je určen  především k seznámení uživatele s požadvky na 
provoz aplikace a k poskytnutí instrukcí k jejímu iniciálnímu zprovoznění. Dále 
jsou zde poskytnuty informace potřebné ke konfiguraci aplikace  a informace pro 
získání  přehledu nad  adresářovou strukturou  projektu. V závěru  textu  je ve 
zkratce vysvětlen princip funkce  frameworku, které přijde vhod v případě zájmu 
o implementaci vlastního rozšíření funkcionality. Pro účely obhajoby práce bude
aplikace dostupná na http://www.sportsclub.cz. Jako přihlašovací údaje lze pou-
žít email z hlavičky tohoto dokumentu a heslo admin.

--------------------------------------------------------------------------------



2) POŽADAVKY NA PROVOZ
================================================================================

Jedním z vedlejších cílů práce byla co nejmenší náročnost na platformu, na které
vytvářený systém poběží. Byly proto zvoleny běžně dostupné, rozšířené a aktivně 
podporované technologie. Výsledkem práce je webová aplikace psaná ve skriptova-
cím jazyce PHP. Základem je tedy webový server s rozšířením pro interpretaci PHP.
V první řadě je důležité poznamenat, že vývoj, provoz a testování byly provedeny
na OS Linux (Ubuntu 12.04.5 LTS). Veškeré postupy proto pramení ze zkušeností s 
touto platformou.

    Apache HTTP server (verze Apache/2.4.10)
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	Aktivní moduly - access_compat, alias, auth_basic, authn_core, 
	    authn_file, authz_core, authz_host, authz_user, autoindex, cgid, 
	    deflate, dir, env, filter, mime, mpm_prefork, negotiation, php5, 
	    rewrite, setenvif, status

    Uživatelské nastavení serveru apache je umístěno v adresáři 
    /etc/apache2/conf-enabled/httpd.conf, s následujícím obsahem:    

    ServerName localhost
    RewriteEngine On
    AddHandler application/x-httpd-php .php5 .php4 .php .php3 .php2 .phtml
    AddType application/x-httpd-php .php5 .php4 .php .php3 .php2 .phtml

    ServerAdmin fuca@localhost
    DocumentRoot /home/fuca/Projects/www
    <Directory />
        Options FollowSymLinks
        AllowOverride None
    </Directory>
    <Directory /home/fuca/Projects/www>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride None All
        Order allow,deny
        allow from all
        Require all granted
    </Directory>

    Systémové nastavení pak /etc/apache2/apache2.conf, do toho však není doporu-
    čené zasahovat a je zde zmíněno jen pro úplnost.

    Pzn.: Po změně nastavení serveru je důležité server apache vždy restartovat. 
	  Toho lze nejsnáze dosáhnout provedním konzolového příkazu 
	  "sudo service apache2 restart".

	    
    PHP5 modul Apache serveru
    ~~~~~~~~~~~~~~~~~~~~~~~~~
    Minimálními požadavky na konfiguraci tohoto modulu jsou zmíněny v samotném 
    textu práce v sekci 5.3.1, je tedy zbytečné je uvádět duplicitně.

	Aktivní moduly - curl, gd, imap, json, mcrypt, memcache, mysqli, mysql, 
	    mysqlnd, opcache, pdo, pdo_mysql, pdo_sqlite, readline, sqlite3, 
	    xdebug, xmlrpc

	Pro nastavení modulu xdebug bylo třeba v adresáři \etc\php5\conf.d vyt-
	vořit inicializační soubor s nastavením xdebug.ini. Jehož obsah je také
	uveden v kapitole 5.3.1 textu práce.
	Direktiva zend_extension=/usr/lib/php5/20121212/xdebug.so určuje umístění
	daného rozšíření, je nutné, aby zde byla platná cesta odpovídající stavu 
	systému, na kterém je aplikace nasazována.
	Další direktiva, která přijde vhod v případě profilování aplikace pomocí
	modulu xdebug je xdebug.profiler_enable_trigger=true.

	Instalaci lze provést sérií příkazů (Zdroj: www.linode.com/docs/websites/
	apache/apache-2-web-server-on-ubuntu-12-04-lts-precise-pangolin):
	    - sudo apt-get install apache2 apache2-doc apache2-utils
	    - apt-get install libapache2-mod-php5 php5 php-pear php5-xcache
	    - sudo apt-get install php5-mysql - Instalace mysql podpory do php5.

    K nastavení samotného php modulu slouží tzv. soubor php.ini, který je umís-
    těn v adresáři /etc/php5/apache2/conf.d.
	

    MySQL databázový server (verze 5.5.38)
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Nastavení mysql serveru nebylo pro tvorbu této práce nijak měněno. Server 
    tedy využívá defaultního nastavení verze dodávané s distribucí 
    Ubuntu 12.04.01 LTS.
    
    Úpravy konfigurace je však možné provádět v adresáři /etc/mysql/my.cnf.

    Je však nutné mít vytvořejný databázový účet, pod kterým bude aplikace na 
    server přistupovat. Tyto informace se pak musí zapsat do konfigurace aplika-
    ce popsané dále v tomto textu.

	Dodatečnou instalaci serveru lze provést provedením sérií příkazů:
	    - sudo apt-get install mysql-server - Nainstaluje server.
	    - mysqladmin create <sportsclub> - Vytvoří databázi sportsclub.


    Google Chrome webový prohlížeč
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Jelikož předmětem práce je webová aplikace, samozřejmostí je také přítomnost 
    webového prohlížeče. Vývoj byl prováděn na prohlížeči Google Chrome verze 
    37.0.2062.120 (64-bit).

--------------------------------------------------------------------------------



3) POPIS ADRESÁŘOVÉ STRUKTURY APLIKACE
================================================================================

Tato sekce slouží k získání přehledu v adresářové struktuře aplikace.

sportclub/		    ~ Kořenový adresář projektu.
|-- app/
|   |-- modules		    ~ Jednotlivé moduly aplikace
|   |	|-- ...
|   |	|-- SystemModule
|   |   |   |-- components  ~ Komponenty definované v rámci modulu
|   |   |   |-- config	    ~ Konfigurační soubory modulu
|   |   |   |-- forms	    ~ Formuláře definované v rámci modulu
|   |   |   |-- locale	    ~ Lokalizační soubory
|   |   |   |-- model	    ~ Soubory modelové vrsty
|   |   |   |-- presenters  ~ Presentery 
|   |   |   `-- templates   ~ Soubory šablon
|   |	`-- ...	
|   |
|   |-- router	    ~ Definice rout systému
|   `-- bootstrap.php 
|
|-- bin/	    ~ Externí skripty
|-- doc/	    ~ API dokumentace v HTML
|-- log/	    ~ Soubory s logy a uložené výjimky
|-- temp/	    ~ Dočasné soubory (cache)
|-- tests/	    ~ Testovací soubory
|-- vendor/	    ~ Knihovny
|-- www/	    ~ Adresář dostupný z webového prohlížeče
|   |-- assets	    ~ Datové zdroje vytvářených entit v aplikaci
|   |-- css	    ~ Kaskádové styly aplikace
|   |-- img	    ~ Designové prvky aplikace
|   |-- js	    ~ JavaScripty použité v aplikaci
|   `-- index.php   ~ Vstupní bod do aplikace
|
|-- LICENSE ~ Úplný výklad použité licence
|-- apigen.neon	    ~ Konfigurační soubor nástroje pro generaci api dokumentace
|-- apigen.phar	    ~ Nástroj pro generaci api dokumentace
|-- capi.sh	    ~ Shell skript pro vytvoření nové API dokumentace
|-- clean_cache.sh  ~ Shell skript pro vyčištění cache
|-- rebuild_db_schema.sh    ~ Shell skript pro znovuvytvoření schéma DB z entit
`-- tt.sh	    ~ Shell skript pro spuštění testů z adresáře tests

Pzn.: Skripty je nutné spouštět z kořenového adresáře projektu.

--------------------------------------------------------------------------------



4) INSTRUKCE PRO NASAZENÍ
================================================================================

V této sekci je popsán postup nasazení aplikace ve výše popsaném funkčním 
prostředí. Předpokladem je přítomnost rozbaleného zip archivu sportsclub.zip z 
elektonické přílohy práce v kořenovém adresáři adresáři Apache2 serveru 
(v httpd.conf uvedeného jako DocumentRoot).


    Inicializace databáze
    ~~~~~~~~~~~~~~~~~~~~~
    Předpokladem je existence prázdné databáze. Pro uvedení projektu do provozu
    je nutné inicializovat databázové schéma. To lze provést dvěma způsoby:
	a) Importem schématu ze souboru app/bin/dump_sql.sql, tím dosáhneme 
	    vytvoření prázné struktury databáze
	b) Provedením konzolového příkazu:
	    - php www/index.php \orm:schema:up --force - Tím dojde k validaci da-
	    tabázového schématu a následnému aplikování do databáze.

	   Pozor! Před provedením tohoto příkazu však musí být vypnuty iniciali-
	   zátory stavu aplikace. Toho dosáhneme nastavením parametru "turnOffInit"
	   v \app\modules\SystemModule\config\applicationConfig.neon na
	   true. Tím se zamezí volání inicializačních procedur při vytváření 
	   DI kontejneru aplikace. Po inicializaci schématu, je potřeba tento pa-
	   rametr opět nastavit na false, aby při prvním spuštění proběhlo uvede-
	   ní aplikace do použitelného stavu.

    Inicializace adresářové struktury
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Aplikace při svém běhu pracuje s adresářovou strukturou, kde spouští, tvoří 
    a mění různé soubory a adresáře. Z tohoto důvodu je nutné nastavit příslučná
    práva. 
	a) Soubor www/index.php musí být spustitelný (executable).

	b) Adresáře temp/ a www/assets/ vývojář potřebuje nanejvýš pro čtení, je 
	tedy vhodné dát aplikaci plnou kontrolu, například vlastnictvím.
	To zařídí příkaz: sudo chown -R www-data:www-data temp/ www/assets/

	c) Do adresáře log/ musí aplikace moci zapisovat. Uživateli se však hodí
	vytvářené logy i měnit, na to je třeba při nastavování práv myslet.

	d) Všechny ostatní skripty (i knihoven ve vendor/) musí být samozřejmě 
	spustitelné, to se týká i adresáře www.	
    
    Nedostatečné nastavení  přístupových práv je velmi  častou příčinou problémů 
    při prvotním nasazení aplikace.

    Užitečné příkazy
    ~~~~~~~~~~~~~~~~
    Při instalaci závislostí Composerem občas dojde k vygenerování neuplného
    autoloaderu. Opětovné vytvoření lze vynutit příkazem composer dump-autoload.

    V textu byl zmíněn příkaz pro aplikaci schématu do databáze. V některých 
    případech příjde vhod pouhé ověření validity schématu a správného mapovaní
    objektů. K tomuto slouží příkaz: php www/index.php \orm:validate.

    Podobně užitečné může být vygenerování SQL skriptu pro import schématu 
    ručně. Příkaz "php www/index.php \orm:schema:up --dump-sql > dump_sql.sql"
    vygeneruje schéma a výstup přesměruje do souboru dump_sql.sql.

    Při vývoji aplikace je dost často nutné vymazání cache paměti. K usnadnění 
    vymazání cache v adresáři temp slouží shell skript clean_cache.sh. Příkaz
    sh ./clean_cache.sh vymaže cache soubory, ale neovlivní session přihlášeného 
    uživatele.

    Provedením změn ve struktuře DB schématu dojde k vytvořením rozdílů mezi 
    schématem definovaným v aplikaci a tím nahraným v databázi. Ke "setření" 
    těchto rozdílů slouží skript rebuild_db_schema.sh, který porovná tyto dvě 
    schémata a aplikuje případné změny.

    Pro spuštení automatizovaných testů v adresáři tests slouží skript tt.sh.

    Po přidání funkcionality se jistě hodí i aktualizace API dokumentace, ta se 
    provádí spuštením skriptu capi.sh.
    

--------------------------------------------------------------------------------



5) NETTE PRESENTER
================================================================================

V této sekci je povrchně nastíněno zpracování požadavku ve frameworku Nette.
Vhodným doplňkem je obrázek životního cyklu presenteru v el. příloze této práce.
Následující text má však primárně sloužit jako nápověda při studiu zdrojových 
kódů aplikace.

Jak bylo řečeno v textu práce, Nette framework je postaven na MVP architektuře. 
To obecně znamená, že stěžejním prvkem je instance Presenteru. Tyto instance za-
jišťují přenos dat mezi modelovou a pohledovou vrstvou, případně i její zpraco-
vání. Z návrhového hlediska si Presenter lze představit jako instanci stránky.
Z toho plyne, že presenterů se v aplikaci může vyskytovat mnoho a také tomu tak 
je. 

Presenter je tedy třída obsahující metody. Metodám adresovatelným napříč ap-
likací se v terminologii Nette frameworku říká "akce". HTTP požadavek obvykle 
vede na nějakou akci příslušného Presenteru. S danou akcí je spjata její ša-
blona, které se v rámci obsluhy požadavku naplní daty a odešle na výstup. Tato 
fáze se nazývá renderování. Uvnitř třídy Nette frameworku lze tedy najít metody
(nebo alespoň jednu z nich) actionXXX a renderXXX, kde XXX je název dané akce.

Dalším funkčním konstruktem presenteru je obsluha signálu, což je metoda začína-
jící prefixem "handle". Jedná se o reakci na "podprahový" signál, který nevede
na překreslení celé stránky. Tato funkcionalita poskytuje základ pro asynchronní
zpracování například při použítí technologie AJAX.

Další metodou, která je velmi častou součástí presenterů je tzv. tovární metoda
(slangově továrnička). Jejím úkolem je vrátit instanci vykreslitelné komponenty.
Tohoto je masivně využíváno při práci se znovupoužitelnými komponentami. Výhodou
je volání tovární metody až ve chvíli, kdy je opravdu potřeba. Jejím povinným 
znakem je prefix "createComponent".

Tyto komponenty jsou neodmyslitelnou součástí frameworku a v aplikaci je jich 
využíváno pro tvorbu gridů, formulářů a navigací. 

DI kontejneru byla věnována kapitola v textové části práce. Detailnější pohled na 
konfiguraci aplikace nabídne následující sekce.

Užitečné odkazy
~~~~~~~~~~~~~~~
    MVP a presentery
    http://doc.nette.org/cs/2.2/presenters

--------------------------------------------------------------------------------



6) KONFIGURACE APLIKACE
================================================================================

Tato sekce slouží k bližšímu seznámení s konfigurací aplikace.

Jak bylo zmíněno konfigurace aplikace je zapsána v konfiguračním souboru:
app/SystemModule/config/applicationConfig.neon, tento soubor slouží ke konfigu-
raci prostředí systému a připojovaným rozšířením. Pro přehlednost je komentáři
rozdělen do 5ti částí:
    - FRAMEWORK SETTINGS
    - 3RD PARTY EXTENSIONS SETTINGS
    - APP EXTENSIONS SETTINGS
    - COMMON SERVICES SETTINGS
    - EXTENSIONS REGISTRATION

Konfigurační direktivy se zapouzdřují do sekcí, které ovlivňují, a pod kterými 
jsou dostupny uvnitř aplikace.

Jelikož konfigurační soubor je opatřen komentáři, zmíníme se zde jen o hrubé 
struktuře některých částí.

Části "3RD PARTY EXTENSIONS SETTINGS" a "APP EXTENSIONS SETTINGS" se obě zabýva-
jí konfigurací rozšíření, jsou odděleny jen ze sémantického hlediska původu roz-
šíření. Registrací (aktivací) rozšíření v části "EXTENSIONS REGISTRATION", vzni-
kne se zadefinuje sekce s jeho názvem před dvojtečkou. Pod tímto názvem je dos-
tupná konfigurační sekce daného rozšíření. Pro lepší pochopení postačí nahlédnu-
tí do konfiguračního souboru.

Velmi důležitým pojmem při tvorbě aplikací v Nette frameworku je registrace slu-
žeb. K tomu slouží sekce "services" v hlavním konfiguračním souboru alikace. V 
konkrétním případě část "COMMON SERVICES SETTINGS". Stejným principem se provádí
registrace služeb v rámci jednotlivých modulů, v jejich konfiguračních souborech,
které se jmenují config.negon a jsou umístěny ve stejnojmenném adresáři ve struk-
tuře modulu.

Konfigurační část "APP EXTENSIONS SETTINGS" je věnovaná nastavení modulů vytvá-
řených v rámci aplikace. Zvláštní pozornost si zde zaslouží inicializační části
označené "init" klíčem. Jejich hodnoty jsou pak datové položky entit, které mají
být přidány při provedení incializačních procedur při zpracování konfigurace. 
Inicializátory modulů kontrolují, zda jsou v aplikaci přítomny inicializační en-
tity, když tomu tak není, zařídí jejich přidání. Existence inicializačních entit
je nutná z důvodu existence administrátorského účtu, který mý právo používat 
všechny zdroje aplikace. Bez existence tohoto systémového uživatele by nebylo 
možné aplikaci začít používat.

Mimo hlavního konfiguračního souboru je přítomen i konfigurační soubor s nastave-
ním specifickým pro prostředí, ve kterém aplikace běží. Základním příkladem je
například nastavení databázového připojení. Tento konfigurační soubor se jmenuje 
applicationConfig.local.neon. Odděleny jsou z důvodu verzování zdrojových kódů.
Tento "lokální" soubor se jednoduše nechá ignorovat verzovacím nástrojem a zbylá 
konfigurace systému pak může být bez problémů zveřejněna.

Užitečné odkazy
~~~~~~~~~~~~~~~
    Konfigurace Frameworku
    http://doc.nette.org/cs/2.2/configuring

--------------------------------------------------------------------------------



7. LICENCE
================================================================================

Toto dílo je licencováno licencí Apache v2.0. Její kompletní znění je uvedeno v 
souboru LICENSE.

--------------------------------------------------------------------------------



8) UPOZORNĚNÍ
================================================================================

Je kriticky důležité, aby `app/modules/SysteModule/config/applicationConfig.neon` 
a celé adresáře `app`, `log` a `temp` nebyly přístupné skrze webový prohlížeč. 
Pokud neboudu tyto adresáře zabezpečeny, kdokoliv bude moci přistoupit k citli-
vým údajům uchovávaným uvnitř systému. Autor práce v žádném případě neručí za 
škody způsobené nevhodným použití této aplikace. Více detailů ohledně bezpečnost
ních rizik lze nalézt na stránce Nette frameworku (nette.org/security-warning).

--------------------------------------------------------------------------------