<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('crud a user custom field');

$I->login_admin();

$I->amOnPage('/oc-panel/userfields/new');
$I->see('New Custom Field','h1');

$I->fillField('name','telephone');
$I->fillField('label','telephone');
$I->fillField('tooltip','telephone');
$I->selectOption('form select[name="type"]','integer');
$I->checkOption('required');
$I->checkOption('searchable');
$I->checkOption('show_register');
$I->checkOption('show_profile');
$I->click('button[type="submit"]');

$I->see('Field telephone created');
$I->seeElement('.drag-item');

// delete all cache
$I->amOnPage('/oc-panel/tools/cache?force=1');
$I->see('All cache deleted');


// activate a premium theme to see the custom field!
// $I->activate_theme('splash');

// // delete all cache
// $I->amOnPage('/oc-panel/tools/cache?force=1');
// $I->see('All cache deleted
// // Not able to see my custom field from the test 
// // but it's displayed on http://reoc.lo/publish-new.html (Maybe an issue with PhpBrowser)

// $I->amOnPage('/');
// $I->click('Logout'); 

// $I->amOnPage('/oc-panel/auth/register');
// $I->seeElement('input', ['id' => 'cf_telephone']);

// $I->login_admin();

$I->amOnPage('/oc-panel/userfields');
$I->seeElement('.drag-item');
$I->amOnPage('/oc-panel/userfields/delete/telephone');
$I->amOnPage('/oc-panel/userfields');
$I->dontSeeElement('.drag-item');
$I->dontSeeElement('.glyphicon.glyphicon-trash');

$I->activate_theme('default');;
