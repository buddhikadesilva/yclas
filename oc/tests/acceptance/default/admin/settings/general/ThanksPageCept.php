<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('create and use a Thanks page');

$I->login_admin();

// Create the page
$I->amOnPage('/oc-panel/content/page');
$I->click('a[href="http://reoc.lo/oc-panel/content/create?type=page"]');
$I->see('Create Page');

$I->fillField('#title','Custom thanks page');
$I->fillField('#description','Thanks for posting!!');
$I->fillField('#seotitle','my-thanks-page');
$I->checkOption('status');
$I->click('button[type="submit"]');
$I->see('page is created. Please to see the changes delete the cache');

// Select page as thanks page
$I->amOnPage('/oc-panel/Config/update/thanks_page');
$I->fillField('#formorm_config_value','my-thanks-page');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// Post new ad
$I->amOnPage('/publish-new.html');
$I->see('Publish new advertisement','h1');
$I->fillField('#title',"Ad test thanks page");
$I->click('.select-category');
$I->fillField('category','18');
$I->fillField('location','4');
$I->fillField('#description','Test thanks page');
// $I->attachFile('input[id="fileInput0"]', 'photo.jpg');
$I->fillField('#phone','99885522');
$I->fillField('#address','barcelona');
$I->fillField('#price','25');
$I->fillField('#website','https://www.admin.com');
$I->click('submit_btn');

$I->see('Advertisement is posted. Congratulations!');

$I->see('Custom thanks page','h1');
$I->see('Thanks for posting!!');
$I->see('Go to Your Ad','a');

$I->amOnPage('/oc-panel/Config/update/thanks_page');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/content/page');
$I->click('a[title="Are you sure you want to delete?"]');
$I->amOnPage('/my-page.html');
$I->see('Page not found','h2');

