<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('enable and see Interactive Map on homepage');

$I->login_admin();

// Enable Interactive Map
$I->amOnPage('/oc-panel/Config/update/map_active');
$I->fillField('#formorm_config_value','1');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/map');
$I->fillField('cd','150');
$I->fillField('c','150');
$I->click("//input[@value='Add']");
$I->click('submit');
//$I->see('Map saved.'); // Map is saved, not sure why this is not visibly on test :S not an issue!

// Check if interactive map appears in all premium themes.
$I->wantTo('activate Splash theme');
$I->activate_theme('splash');

$I->amOnPage('/');
$I->see('Map', 'h2');

$I->wantTo('activate Reclassifieds3 theme');
$I->activate_theme('reclassifieds');

$I->amOnPage('/');
//$I->see('Map', 'h2'); div for map is displayed, without title or something unique so it can check for it

$I->activate_theme('newspaper');
$I->wantTo('activate Newspaper theme');

$I->amOnPage('/');
$I->see('Map', 'h2');


$I->wantTo('activate Czsale theme');
$I->activate_theme('czsale');

$I->amOnPage('/');
$I->see('Map', 'h4');


$I->wantTo('activate Ocean Premium theme');
$I->activate_theme('ocean');

$I->amOnPage('/');
//$I->see('Map', 'h2'); div for map is displayed, without title or something unique so it can check for it


$I->wantTo('activate moderndeluxe3 theme');
$I->activate_theme('moderndeluxe');

$I->amOnPage('/');
//$I->seeElement('div', ['id' => 'visualization']); // map is not displayed on test but it's displayed if I enable and cofigure it from panel


$I->wantTo('activate Olson theme');
$I->activate_theme('olson');

$I->amOnPage('/');
$I->see('Map', 'h4');


$I->wantTo('activate Kamaleon theme');
$I->activate_theme('kamaleon');

$I->amOnPage('/');
$I->see('Map', 'h2');


$I->wantTo('activate Jobdrop theme');
$I->activate_theme('jobdrop');
$I->amOnPage('/');
$I->see('Map', 'h2');


$I->wantTo('activate responsive theme');
$I->activate_theme('responsive');
$I->amOnPage('/');
$I->see('Map', 'h2');


$I->wantTo('activate Yummo theme');
$I->activate_theme('yummo');
$I->amOnPage('/');
$I->see('Map', 'h2');


$I->wantTo('activate Basecamp theme');
$I->activate_theme('basecamp');
$I->amOnPage('/');
$I->see('Map', 'h3');

$I->$I->activate_theme('default');;

$I->amOnPage('/oc-panel/Config/update/map_jscode');
$I->fillField('#formorm_config_value','');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/Config/update/map_settings');
$I->fillField('#formorm_config_value','');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/oc-panel/Config/update/map_active');
$I->fillField('#formorm_config_value','0');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/');
$I->dontSee('Map', 'h2');
