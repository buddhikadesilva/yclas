<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('enable 2step authentication and see that it works');

$I->login_admin();

// Enable google_authenticator
$I->wantTo('enable google_authenticator');
$I->amOnPage('/oc-panel/Config/update/google_authenticator');
$I->fillField('#formorm_config_value','1');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// Check 2 step authentication on profile edit
$I->wantTo('check 2 step authentication on profile edit');
$I->amOnPage('/oc-panel/profile/edit');
$I->see('2 Step Authentication','h3');
$I->seeElement('.btn.btn-primary');
$I->seeElement('.fa.fa-android');
$I->seeElement('.fa.fa-apple');

// Try to enable 2 step authentication and see the code
$I->click('a[href="http://reoc.lo/oc-panel/profile/2step/enable"]');
$I->see('Verification Code','label');
$I->see('2 Step Authentication','h1');

// Chech that 2 step auth is still disabled 
$I->amOnPage('/oc-panel/profile/edit');
$I->see('2 Step Authentication','h3');
$I->seeElement('.btn.btn-primary');
$I->seeElement('.fa.fa-android');
$I->seeElement('.fa.fa-apple');

// Disable google_authenticator
$I->wantTo('disable google_authenticator');
$I->amOnPage('/oc-panel/Config/update/google_authenticator');
$I->fillField('#formorm_config_value','0');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/profile/edit');
$I->dontSee('2 Step Authentication','h3');
$I->dontSeeElement('.fa.fa-android');
$I->dontSeeElement('.fa.fa-apple');
