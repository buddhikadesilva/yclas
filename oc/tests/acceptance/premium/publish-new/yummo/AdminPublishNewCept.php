<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('publish a new ad');

$I->login_admin();

$I->activate_theme('yummo');

$I->amOnPage('/publish-new.html');
$I->see('Publish new advertisement');
$I->fillField('#title',"Admin ad yummo theme");
$I->fillField('#category-selected','18');
$I->fillField('#location-selected','4');
$I->fillField('#description','This is a new admin ad on yummo theme');
// $I->attachFile('input[id="fileInput0"]', 'photo.jpg');
$I->fillField('#phone','99885522');
$I->fillField('#address','barcelona');
$I->fillField('#price','25');
$I->fillField('#website','https://www.admin.com');
$I->click('submit_btn');

$I->see('Advertisement is posted. Congratulations!');

$I->amOnPage('/apartment/admin-ad-yummo-theme.html');
$I->see('Admin ad yummo theme');
$I->see('This is a new admin ad on yummo theme');
$I->see('Barcelona');

$I->activate_theme('default');

$I->click('Logout'); 
