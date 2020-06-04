<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('change themes and theme options for each theme');

$I->login_admin();

// Basecamp_free
$I->activate_theme('basecamp_free');

// Default
$I->activate_theme('default');
