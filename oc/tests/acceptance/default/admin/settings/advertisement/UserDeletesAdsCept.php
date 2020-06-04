<?php 
$I = new AcceptanceTester($scenario);
$I->am("the admin");
$I->wantTo('change configurations and see changes on frontend');

$I->login_admin();

// Enable Delete ads
$I->amOnPage('/oc-panel/Config/update/delete_ad');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Read
$I->amOnPage('/oc-panel/myads');
$I->seeElement('.glyphicon.glyphicon-minus');

// Back to default
$I->amOnPage('/oc-panel/Config/update/delete_ad');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Read
$I->amOnPage('/oc-panel/myads');
$I->dontSeeElement('.glyphicon.glyphicon-minus');
