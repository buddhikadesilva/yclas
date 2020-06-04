<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('update the database');

$I->login_admin();

$I->amOnPage('/oc-panel/update/database?from_version=3.0.0');
$I->see('Software DB Updated to latest version!');

?>