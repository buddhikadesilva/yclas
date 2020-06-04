<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('enable 2step authentication and see that it works');

$I->login_admin();

// Enable sms auth
$I->wantTo('enable sms auth');
$I->amOnPage('/oc-panel/Config/update/sms_auth');
$I->fillField('#formorm_config_value','1');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// Select country
$I->wantTo('select country');
$I->amOnPage('/oc-panel/Config/update/country');
$I->fillField('#formorm_config_value','CY');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->click('Logout');

// Read
$I->amOnPage('/oc-panel/auth/login');
$I->see('Phone Login');
$I->seeElement("input",['data-country' => 'CY']);

$I->amOnPage('/oc-panel/auth/register');
$I->see('Phone Register');
$I->seeElement("input",['data-country' => 'CY']);



$I->login_admin();

// Disable sms auth
$I->wantTo('disable sms auth');
$I->amOnPage('/oc-panel/Config/update/sms_auth');
$I->fillField('#formorm_config_value','0');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->wantTo('select country');
$I->amOnPage('/oc-panel/Config/update/country');
$I->fillField('#formorm_config_value','');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');
