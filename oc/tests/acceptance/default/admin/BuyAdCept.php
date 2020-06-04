<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('buy an ad');

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/paypal_seller');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->click('a[href="http://reoc.lo/ad/buy/3"]');

$I->see('Checkout','h1');
$I->see('Purchase: just-random-title-here');

$I->amOnPage('/oc-panel/Config/update/paypal_seller');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');




