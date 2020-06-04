<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantToTest('the major currencies');

$I->login_admin();

// change currency to AUD
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','AUD');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// see price
$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('AU$300.00');

// change currency
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','CAD');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// see price
$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('CA$300.00');

// change currency
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','EUR');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// see price
$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('€300,00');

// change currency
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','USD');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// see price
$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('$300.00');

// change currency
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','JPY');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// see price
$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('¥300');

// change currency
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','RUB');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// see price
$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('RUB300,00');

// back to default
$I->amOnPage('/oc-panel/Config/update/number_format');
$I->fillField('#formorm_config_value','%n');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('300.00');
