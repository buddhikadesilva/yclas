<?php 
$I = new AcceptanceTester($scenario);
$I->am('a visitor');
$I->amGoingTo('register as a user that already exists');
$I->lookForwardTo('see the error message after trying to register');
$I->amOnPage('/oc-panel/auth/register');
$I->fillField('name','user-admin');
$I->fillField(".register input[name='email']", 'user-admin@gmail.com');
$I->fillField('password1','user');
$I->fillField('password2','user');
$I->click(".register button[type=submit]");
$I->expectTo('see success message');
$I->see('welcome');
$I->amOnPage('/');
$I->click('Logout');

?>