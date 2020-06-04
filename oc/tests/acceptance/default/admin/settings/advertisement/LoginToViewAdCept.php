<?php 
$I = new AcceptanceTester($scenario);
$I->am("the admin");
$I->wantTo('change configurations and see changes on frontend');

$I->login_admin();

// Enable Delete ads
$I->amOnPage('/oc-panel/Config/update/login_to_view_ad');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Logout
$I->amOnPage('/');
$I->click('Logout');

// Read
$I->amOnPage('/jobs/some-nice-title-here.html');
$I->see('Login','h1');
$I->seeInCurrentUrl('login');
$I->seeInCurrentUrl('auth_redirect');

// Login
$I->login_admin();

// Back to default
$I->amOnPage('/oc-panel/Config/update/login_to_view_ad');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Logout
$I->amOnPage('/');
$I->click('Logout');

// Read
$I->amOnPage('/jobs/some-nice-title-here.html');
$I->dontSee('Login','h1');
$I->dontSeeInCurrentUrl('login');
$I->dontSeeInCurrentUrl('auth_redirect');
