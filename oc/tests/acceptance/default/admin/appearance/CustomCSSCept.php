<?php 
$I = new AcceptanceTester($scenario);
$I->am("the admin");
$I->wantTo('enable Custom CSS');

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/custom_css');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/theme/css');
$I->fillField('css','some custom css');
$I->click('submit');
$I->see('CSS file saved');

$I->amOnPage('/themes/default/css/web-custom.css');
$I->see('some custom css');

$I->amOnPage('/oc-panel/Config/update/custom_css');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/');
$I->click('Logout');

