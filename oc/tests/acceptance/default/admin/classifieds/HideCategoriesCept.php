<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('crud a category');

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/hide_homepage_categories');
$I->fillField('formorm[config_value]','["","5"]');
$I->click('formorm[submit]');

$I->amOnPage('/');
$I->dontSee('Housing', '//div/a');
$I->see('Jobs', '//div/a');
$I->see('Market', '//div/a');

$I->amOnPage('/oc-panel/Config/update/hide_homepage_categories');
$I->fillField('formorm[config_value]','["",""]');
$I->click('formorm[submit]');

$I->amOnPage('/');
$I->see('Housing', '//div/a');
$I->see('Market', '//div/a');
$I->see('Jobs', '//div/a');

$I->click('Logout'); 