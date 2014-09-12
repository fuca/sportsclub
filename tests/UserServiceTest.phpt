<?php

require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../tester/Tester/bootstrap.php';

use Tester\Assert;

class UserServiceTest extends Tester\TestCase {
    
    public function setUp() {
        # Příprava
    }

    public function tearDown() {
        # Úklid
    }
    
    public function testOne() {
        //Assert::same(......);
    }

    public function testTwo() {
        //Assert::match(......);
    }
}


$testCase = new UserServiceTest;
$testCase->run();

