<?php 
$I = new AcceptanceTester($scenario);

$I->am('the Administrator');
$I->wantTo('check that ad expiration date and allow ad reactivation work');
$I->lookForwardTo('see the welcome message in the Panel');
$I->login_admin();

// Ad is active
$I->amOnPage('/jobs/');
$I->see('some nice title here','a');
$I->amOnPage('/jobs/some-nice-title-here.html');

// Ad expiration date to 10
$I->amOnPage('/oc-panel/Config/update/expire_date');
$I->fillField('#formorm_config_value','10');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Ad is expired
$I->amOnPage('/jobs/');
$I->dontSee('some nice title here','a');

// Disable ad reactivation
$I->amOnPage('/oc-panel/Config/update/expire_reactivation');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// deactivate the ad manually, normally there's a cro for that
$I->amOnPage('/oc-panel/myads/deactivate/1');
$I->see('Advertisement is deactivated');

// try to activate the ad
$I->amOnPage('/oc-panel/myads/activate/1');
$I->see('Advertisement can not be marked as “active”.');

// Enable ad reactivation
$I->amOnPage('/oc-panel/Config/update/expire_reactivation');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Ad expiration date to 0 - no expiration for ads
$I->amOnPage('/oc-panel/Config/update/expire_date');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// try to activate the ad
$I->amOnPage('/oc-panel/myads/activate/1');
$I->see('Advertisement is active and published');

// Ad is active
$I->amOnPage('/jobs/');
$I->see('some nice title here','a');














?>