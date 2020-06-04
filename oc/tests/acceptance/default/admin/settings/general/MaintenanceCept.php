<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/maintenance');
$I->see('Update Config','h1');
$I->wantTo('enable maintenance mode and see the message on the frontend');
$I->fillField('#formorm_config_value', '1');
$I->click("//button[@type='submit']"); //click save
$I->see('Item updated. Please to see the changes delete the cache');
$I->see('You are in maintenance mode, only you can see the website');
$I->amOnPage('/');
$I->see('You are in maintenance mode, only you can see the website');

////

$I->wantTo('switch OFF maintenance mode');
$I->amOnPage('/oc-panel/Config/update/maintenance');
$I->see('Update Config','h1');
$I->fillField('#formorm_config_value', '0');
$I->click("//button[@type='submit']"); //click save
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/');
$I->dontSee('You are in maintenance mode, only you can see the website');

$I->click("Logout");



