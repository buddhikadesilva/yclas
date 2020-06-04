<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('publish a new ad');

$I->login_admin();

$I->activate_theme('mobile');

$I->amOnPage('/publish-new.html');
$I->see('Publish new advertisement');
$I->fillField('#title',"Admin ad mobile theme");
$I->selectOption('category','18');
$I->selectOption('location','4');
$I->fillField('#description','This is a new admin ad on mobile theme');
$I->fillField('#phone','99885522');
$I->fillField('#address','barcelona');
$I->fillField('#price','25');
$I->fillField('#website','https://www.admin.com');
$I->click('submit');

$I->see('Advertisement is posted. Congratulations!');

$I->amOnPage('/apartment/admin-ad-mobile-theme.html');
$I->see('Admin ad mobile theme');
$I->see('This is a new admin ad on mobile theme');
$I->see('Barcelona');

$I->activate_theme('default');

$I->click('Logout'); 
