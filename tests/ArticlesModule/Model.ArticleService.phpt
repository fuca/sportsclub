<?php


use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * TEST: ArticleServiceTestCase
 */
require __DIR__ . '/../bootstrap.php';

class ArticleServiceTest extends Tester\TestCase {
    
    private $article;
    
    private $container;

    function __construct(Nette\DI\Container $container){
		$this->container = $container;
    }
    
    // Příprava pred kazdou jednou metodou
    public function setUp() {
        
    }

    // Úklid po kazde jedne metode
    public function tearDown() {
     
    }

    public function testCreateArticle(\App\Model\Entities\Article $a) {
	$e = "";
    }

    public function testDeleteArticle($id) {
	
    }

    public function testGetArticle($id) {
	
    }

    public function testGetArticleAlias($alias) {
	
    }

    public function testGetArticles(\App\Model\Entities\SportGroup $g) {
	
    }

    public function testGetArticlesDatasource() {
	
    }

    public function testGetHighLights() {
	
    }

    public function testUpdateArticle(\App\Model\Entities\Article $a) {
	
    }
}

// Spuštění testovacích metod
$testCase = new ArticleServiceTest($container);
$testCase->run();