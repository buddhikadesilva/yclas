<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('enable memberships, CRUD and buy plans');

$I->login_admin();

// Activate Memberships
$I->amOnPage('/oc-panel/Config/update/subscriptions');
$I->fillField('#formorm_config_value','1');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// Create plan
$I->wantTo('activate Ocean Premium theme');
$I->amOnPage('/oc-panel/Plan/create');
$I->fillField('#formorm_name','Free plan');
$I->fillField('#formorm_seoname','free-plan');
$I->fillField('#formorm_description','This is the free plan, one free ad for each seven days');
$I->fillField('#formorm_price','0');
$I->fillField('#formorm_days','7');
$I->fillField('#formorm_amount_ads','1');
$I->checkOption('formorm[status]');
$I->click('formorm[submit]');
$I->see('Item created. Please to see the changes delete the cache');

// Change to Ocean Premium (premium feature)
$I->wantTo('activate Ocean Premium theme');
$I->activate_theme('ocean');

// See plan
$I->wantTo('go to the publish page and see the plan.');
$I->amOnPage('/publish-new.html');
$I->seeElement('.alert.alert-info');
$I->see('Please, choose a plan first');
$I->see('Free plan','h3');
$I->see('This is the free plan, one free ad for each seven days','p');
$I->see('7 days','p');
$I->see('1 Ads','p');
$I->see('Sign Up');
$I->see('$0.00');
$I->seeElement('.btn.btn-success.btn-block');

// Buy plan
$I->wantTo('purchase the plan.');
$I->click("//a[@class='btn btn-success btn-block']");

// Check that I purchased the plan
$I->wantTo('check if I can publish an ad and if the plan is displayed on edit profile page.');
$I->see('Orders','h1');
$I->see('Free plan - This is the free plan, one free ad for each seven days','td');
$I->see('$0.00','td');
$I->amOnPage('/publish-new.html');
$I->see('Publish new advertisement','h1');

$I->amOnPage('/oc-panel/profile/edit');
$I->see('Subscription','h3');
$I->see('You are subscribed to the plan Free plan','p');
$I->see('with 1 ads left','p');

// Update plan
$I->wantTo('upgrade the plan');
$I->amOnPage('/oc-panel/Plan/update/100');
$I->fillField('#formorm_amount_ads','2');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// Login as a user to see the updated plan
$I->amOnPage('/');
$I->click('Logout');  
$I->amOnPage('/oc-panel/auth/login');
$I->fillField('email','gazzasdasd@gmail.com');
$I->fillField('password','1234');
$I->click('auth_redirect'); 
$I->amOnPage('/publish-new.html');
$I->seeElement('.alert.alert-info');
$I->see('Please, choose a plan first');
$I->see('Free plan','h3');
$I->see('This is the free plan, one free ad for each seven days','p');
$I->see('7 days','p');
$I->see('2 Ads','p');
$I->see('Sign Up');
$I->see('$0.00');
$I->seeElement('.btn.btn-success.btn-block');

$I->amOnPage('/');
$I->click('Logout');  

// Login as admin
$I->login_admin();

// Delete plan
$I->amOnPage('/oc-panel/Plan/delete/100');

// Check as a user if the plan exists
$I->amOnPage('/');
$I->click('Logout');  
$I->amOnPage('/oc-panel/auth/login');
$I->fillField('email','gazzasdasd@gmail.com');
$I->fillField('password','1234');
$I->click('auth_redirect'); 

$I->amOnPage('/publish-new.html');
$I->seeElement('.alert.alert-info');
$I->see('Please, choose a plan first');
$I->dontSee('Free plan','h3');

// Login as admin - disable Memberships
$I->amOnPage('/');
$I->click('Logout');  

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/subscriptions');
$I->fillField('#formorm_config_value','0');
$I->click('formorm[submit]');
$I->see('Item updated. Please to see the changes delete the cache');

// Check as a user if memberships are disabled.
$I->amOnPage('/');
$I->click('Logout');  
$I->amOnPage('/oc-panel/auth/login');
$I->fillField('email','gazzasdasd@gmail.com');
$I->fillField('password','1234');
$I->click('auth_redirect'); 

$I->amOnPage('/publish-new.html');
$I->dontSeeElement('.alert.alert-info');
$I->dontSee('Please, choose a plan first');

// Login as admin - switch to default theme
$I->amOnPage('/');
$I->click('Logout');

$I->login_admin();

$I->$I->activate_theme('default');;



