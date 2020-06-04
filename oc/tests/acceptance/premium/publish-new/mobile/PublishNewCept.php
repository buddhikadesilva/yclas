<?php 
$I = new AcceptanceTester($scenario);
$I->am('a user');
$I->wantTo('publish a new ad');

$I->login_admin();

$I->activate_theme('mobile');

$I->click('Logout'); 

$I->amOnPage('/oc-panel/auth/login');
$I->fillField('email','gazzasdasd@gmail.com');
$I->fillField('password','1234');
$I->click('auth_redirect');

$I->amOnPage('/publish-new.html');
$I->see('Publish new advertisement');
$I->fillField('#title',"User ad on mobile");
$I->selectOption('category','18');
$I->selectOption('location','4');
$I->fillField('#description','This is a new user ad on mobile theme');
$I->fillField('#phone','99885522');
$I->fillField('#address','barcelona');
$I->fillField('#price','25');
$I->fillField('#website','https://www.admin.com');
$I->click('submit');

$I->see('Advertisement is posted. Congratulations!');

$I->amOnPage('/apartment/user-ad-on-mobile.html');
$I->see('User ad on mobile');
$I->see('This is a new user ad on mobile theme');
$I->see('Barcelona');

$I->click('Logout'); 

$I->login_admin();

$I->activate_theme('default');

$I->click('Logout'); 