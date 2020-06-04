<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('publish a new ad');

$I->login_admin();

$I->activate_theme('reclassifieds');

$I->amOnPage('/publish-new.html');
$I->see('Publish new advertisement');
$I->fillField('#title',"Admin ad reclassifieds theme");
$I->click('.select-category');
$I->fillField('category','18');
$I->fillField('location','57');
$I->fillField('#description','This is a new admin ad on reclassifieds theme');
// $I->attachFile('input[id="fileInput0"]', 'photo.jpg');
$I->fillField('#phone','99885522');
$I->fillField('#address','barcelona');
$I->fillField('#price','25');
$I->fillField('#website','https://www.admin.com');
$I->click('submit_btn');

$I->see('Advertisement is posted. Congratulations!');

$I->amOnPage('/apartment/admin-ad-reclassifieds-theme.html');
$I->see('Admin ad reclassifieds theme');
$I->see('This is a new admin ad on reclassifieds theme');
$I->see('Barcelona');

$I->activate_theme('default');

$I->click('Logout'); 
