<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('crud a location');

$I->login_admin();

$I->amOnPage('/oc-panel/location');
$I->click('a[href="http://reoc.lo/oc-panel/location/create"]');
$I->see('New Location');

$I->fillField('#name','Loc');
$I->fillField('#seoname','my-new-loc');
$I->fillField('#description','This is my new location');
$I->click('button[type="submit"]');

$I->see('Location created');

$I->amOnPage('/oc-panel/location');
$I->see('Loc');

$I->click('Logout'); 
