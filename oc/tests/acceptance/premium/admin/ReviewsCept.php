<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('crud a custom field');

$I->login_admin();

// enable reviews
$I->amOnPage('/oc-panel/Config/update/reviews');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// check all themes theme to premium

$I->activate_theme('splash');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');

$I->activate_theme('reclassifieds');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('reclassifieds');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('newspaper');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('czsale');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('ocean');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('moderndeluxe');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('olson');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');


$I->activate_theme('kamaleon');

$I->amOnPage('/jobs/just-random-title-here.html');
$I->see('leave a review','a');



$I->$I->activate_theme('default');;

$I->amOnPage('/oc-panel/Config/update/reviews');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->click('Logout'); 

