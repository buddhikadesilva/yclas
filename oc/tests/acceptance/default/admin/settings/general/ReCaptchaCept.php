<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('enable ReCaptcha');

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/recaptcha_active');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');
$I->amOnPage('/oc-panel/Config/update/recaptcha_secretkey');
$I->fillField('#formorm_config_value','6LcN3AATAAAAAAJM-ef2dL1zBMyUZpATTIr0ubln');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');
$I->amOnPage('/oc-panel/Config/update/recaptcha_sitekey');
$I->fillField('#formorm_config_value','6LcN3AATAAAAAPpqImSRX56OBFEzYKnxdzQzLN6L');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/publish-new.html');
$I->seeElement('#recaptcha1');

$I->amOnPage('/oc-panel/Config/update/recaptcha_active');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');
$I->amOnPage('/oc-panel/Config/update/recaptcha_secretkey');
$I->fillField('#formorm_config_value','');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');
$I->amOnPage('/oc-panel/Config/update/recaptcha_sitekey');
$I->fillField('#formorm_config_value','');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/publish-new.html');
$I->dontSeeElement('#recaptcha1');

