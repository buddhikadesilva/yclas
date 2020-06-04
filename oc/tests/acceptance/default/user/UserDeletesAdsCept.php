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

$I->click('Logout');

$I->amOnPage('/oc-panel/auth/login');
$I->fillField('email','gazzasdasd@gmail.com');
$I->fillField('password','1234');
$I->click('auth_redirect');
$I->see('my profile');

// Read
$I->amOnPage('/oc-panel/myads');
$I->seeElement('.glyphicon.glyphicon-minus');

$I->amOnPage('/oc-panel/myads/delete/19');
$I->see('Advertisement deleted');

$I->click('Logout');

// Back to default
$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/delete_ad');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Read
$I->amOnPage('/oc-panel/myads');
$I->dontSeeElement('.glyphicon.glyphicon-minus');
