<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('add html code in head and footer element');

$I->login_admin();

// Fill the fields
$I->amOnPage('/oc-panel/Config/update/html_head');
$I->fillField('#formorm_config_value','This is a text I want to see on frontend in HEAD');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/Config/update/html_footer');
$I->fillField('#formorm_config_value','This is a text I want to see on frontend in FOOTER');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Read
$I->amOnPage('/');
// HEAD
$I->see('This is a text I want to see on frontend in HEAD');
// FOOTER
$I->see('This is a text I want to see on frontend in FOOTER');


// Back to default
$I->amOnPage('/oc-panel/Config/update/html_head');
$I->fillField('#formorm_config_value','');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/Config/update/html_footer');
$I->fillField('#formorm_config_value','');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/');
// HEAD
$I->dontSee('This is a text I want to see on frontend in HEAD');
// FOOTER
$I->dontSee('This is a text I want to see on frontend in FOOTER');




