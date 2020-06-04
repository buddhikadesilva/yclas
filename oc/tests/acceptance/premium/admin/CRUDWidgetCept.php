<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('CRUD widgets');

$I->login_admin();

// Categories
$I->wantTo('create a widget');
$I->amOnPage('/oc-panel/widget');
$I->click('Create');
$I->selectOption('placeholder','sidebar');
$I->click('Save changes');

// See on default theme
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on splash theme
$I->activate_theme('splash');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on moderndeluxe3 theme
$I->activate_theme('moderndeluxe');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on olson theme
$I->activate_theme('olson');
$I->amOnPage('/all');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on reclassifieds3 theme
$I->activate_theme('reclassifieds');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on kamaleon theme
$I->activate_theme('kamaleon');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on responsive theme
$I->activate_theme('responsive');
$I->amOnPage('/');
$I->seeElement('.widget-header');

// See on czsale theme
$I->activate_theme('czsale');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on jobdrop theme
$I->activate_theme('jobdrop');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('sidebar_position','right');
$I->click('submit');
$I->amOnPage('/all');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('sidebar_position','none');
$I->click('submit');

// See on ocean theme
$I->activate_theme('ocean');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on yummo theme
$I->activate_theme('yummo');
$I->amOnPage('/');
$I->seeElement('.col-md-3.col-sm-12.col-xs-12');

// See on newspaper theme
$I->activate_theme('newspaper');
$I->amOnPage('/');
$I->seeElement('#sidebar');

// See on basecamp theme
$I->activate_theme('basecamp');
$I->amOnPage('/all');
$I->seeElement('.Widget_Search');

// Back to default theme
$I->$I->activate_theme('default');;

// Delete
$I->amOnPage('/oc-panel/widget');
$I->click('button[class="btn btn-primary btn-xs pull-right"]');
$I->seeElement('.glyphicon.glyphicon-trash');
$I->click('a[class="btn btn-danger pull-left"]');

$I->amOnPage('/');
$I->click('Logout');























