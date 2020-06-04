<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('change themes and theme options for each theme');

$I->login_admin();

// Splash

$I->activate_theme('splash');

$I->amOnPage('/oc-panel/theme/options');
$I->fillField('#home_slogan','Homepage site slogan');
$I->click('submit');
$I->see('Theme configuration updated');

$I->amOnPage('/');
$I->see('Homepage site slogan');

$I->amOnPage('/oc-panel/theme/options');
$I->fillField('#home_slogan','Search and place ads easily with us');
$I->click('submit');
$I->see('Theme configuration updated');

$I->amOnPage('/');
$I->see('Search and place ads easily with us');


// Reclassifieds

$I->activate_theme('reclassifieds');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// See on responsive theme
$I->activate_theme('responsive');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('listing_slider','1');
$I->click('submit');
$I->amOnPage('/all');
$I->seeElement('.well.featured-posts');
$I->seeElement('.glyphicon.glyphicon-chevron-right');
$I->seeElement('.glyphicon.glyphicon-chevron-left');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('listing_slider','0');
$I->click('submit');


// Newspaper
$I->activate_theme('newspaper');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// Czsale
$I->activate_theme('czsale');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// Ocean
$I->activate_theme('ocean');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// Modern Deluxe
$I->activate_theme('moderndeluxe');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// Olson
$I->activate_theme('olson');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// Kamaleon
$I->activate_theme('kamaleon');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');


// Jobdrop
$I->activate_theme('jobdrop');

$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');


// Yummo
$I->activate_theme('yummo');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('listing_slider','1');
$I->click('submit');
$I->amOnPage('/all');
$I->seeElement('.featured-posts');
$I->seeElement('.glyphicon.glyphicon-chevron-right');
$I->seeElement('.glyphicon.glyphicon-chevron-left');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('listing_slider','0');
$I->click('submit');


// Basecamp_free
$I->activate_theme('basecamp_free');

// Basecamp
$I->activate_theme('basecamp');

$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','0');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->dontSeeElement('.breadcrumb');
$I->amOnPage('/oc-panel/theme/options');
$I->selectOption('breadcrumb','1');
$I->click('submit');
$I->see('Theme configuration updated');
$I->amOnPage('/housing');
$I->seeElement('.breadcrumb');

// Mobile
$I->activate_theme('mobile');


// Default
$I->$I->activate_theme('default');;

