<?php 
$I = new AcceptanceTester($scenario);

// Login successfully as the administrator
$I->am('the Administrator');
$I->wantTo('log in with valid account');
$I->lookForwardTo('see the welcome message in the Panel');
$I->login_admin();

$I->wantTo('switch ON "only administrator can publish new ad"');
$I->amOnPage('/oc-panel/Config/update/only_admin_post');
$I->fillField("formorm[config_value]","1");
$I->click('button[type="submit"]');

$I->wantTo('logout and not to see publish new button');
$I->click("//a[@href='http://reoc.lo/oc-panel/auth/logout']");
$I->amOnPage('/');
$I->dontsee('publish new');

// bring it back to default option!
// login
$I->login_admin();
// switch off only administrator can publish new ad
$I->amOnPage('/oc-panel/Config/update/only_admin_post');
$I->fillField("formorm[config_value]","0");
$I->click('button[type="submit"]');

// logout and check if i can see publish new button
$I->click("//a[@href='http://reoc.lo/oc-panel/auth/logout']");
$I->see('login');
$I->amOnPage('/');
$I->see('publish new');

















?>