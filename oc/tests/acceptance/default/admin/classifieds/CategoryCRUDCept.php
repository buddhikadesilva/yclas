<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('crud a category');

$I->login_admin();

// Create
$I->amOnPage('/oc-panel/category/create');
$I->see('New Category');
$I->fillField('#formorm_name','My New Category');
$I->fillField('#formorm_seoname','my-new-category');
$I->fillField('#formorm_description','This is my new category');
$I->click('button[type="submit"]');
$I->see('Category created');

// Read
$I->amOnPage('/my-new-category');
$I->dontSee('Page not found');
$I->see('This is my new category');

// Update
// Not unique button

// Delete
// Not unique button 

$I->click('Logout'); 