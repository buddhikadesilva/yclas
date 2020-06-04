<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('edit a user');

$I->login_admin();

$I->amOnPage('/oc-panel/User/update/2');
$I->fillField('password1','1234');
$I->fillField('password2','1234');
$I->click("//form[contains(@action,'http://reoc.lo/oc-panel/user/changepass/2')]/div/div/button[@type='submit']");
$I->see('Password is changed');

$I->amOnPage('/oc-panel/User/update/3');
$I->fillField('password1','1234');
$I->fillField('password2','1234');
$I->click("//form[contains(@action,'http://reoc.lo/oc-panel/user/changepass/3')]/div/div/button[@type='submit']");
$I->see('Password is changed');

$I->amOnPage('/oc-panel/User/update/4');
$I->fillField('password1','1234');
$I->fillField('password2','1234');
$I->click("//form[contains(@action,'http://reoc.lo/oc-panel/user/changepass/4')]/div/div/button[@type='submit']");
$I->see('Password is changed');
