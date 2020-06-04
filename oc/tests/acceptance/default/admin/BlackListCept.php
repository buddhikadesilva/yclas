<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('mark an ad as spam, see the owner of the ad in Black List, remove him, remove spam from ad.');

$I->login_admin();

$I->amOnPage('/oc-panel/ad');
$I->click('a[href="http://reoc.lo/oc-panel/ad/spam/4?current_url=1"]');
$I->see('john@gmail.com has been disable for posting, due to recent spam content!');
$I->see('Advertisement is marked as spam');

$I->amOnPage('/oc-panel/pool');
$I->see('John Smith');


$I->amOnPage('/oc-panel/User/update/4');
$I->fillField('password1','1234');
$I->fillField('password2','1234');
$I->click("//form[contains(@action,'http://reoc.lo/oc-panel/user/changepass/4')]/div/div/button[@type='submit']");
$I->seeElement('.alert.alert-success');
$I->see('Password is changed');

$I->amOnPage('/');
$I->click('Logout');

$I->amOnPage('/oc-panel/auth/login');
$I->fillField('email','john@gmail.com');
$I->fillField('password','1234');
$I->click('auth_redirect');
$I->amOnPage('/publish-new.html');
$I->seeElement('.alert');
$I->see('Your profile has been disable for posting, due to recent spam content! If you think this is a mistake please contact us.');

$I->click('Logout');

$I->login_admin();


$I->amOnPage('/oc-panel/pool');
$I->seeElement('.btn.btn-danger');

$I->click('a[href="http://reoc.lo/oc-panel/pool/remove/4"]');
$I->seeElement('.alert.alert-success');

$I->amOnPage('http://reoc.lo/oc-panel/ad/activate/4?current_url=30');
$I->see('Advertisement is active and published');








