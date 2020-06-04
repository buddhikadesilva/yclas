<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('check that all the pages in the panel open');

$I->login_admin();

$I->amOnPage('/oc-panel/');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/stats/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/update/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/ad/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/category/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/location/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/fields/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/coupon/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/content/page');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/content/email');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/newsletter/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/cmsimages/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/map');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/theme/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/theme/options');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/widget/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/menu/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/theme/css');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/market/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/settings/general');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/settings/form');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/settings/email');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/settings/payment');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/settings/plugins');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/translations/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/settings/image');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/user/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/role/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/pool/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/tools/optimize');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/crontab/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');

$I->amOnPage('/oc-panel/import/index');
$I->dontSee('ErrorException');
$I->dontSee('Undefined');
