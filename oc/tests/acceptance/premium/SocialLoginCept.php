<?php 
$I = new AcceptanceTester($scenario);
$I->am('the administrator');
$I->wantTo('enable social login and see the buttons on login page');

$I->login_admin();

$I->amOnPage('/oc-panel/Config/update/config');
$I->fillField('#formorm_config_value','{"debug_mode":"0","providers":{"OpenID":{"enabled":"1"},"Yahoo":{"enabled":"1","keys":{"id":"dj0yJmk9UFc1ZHo0YUdmTEtvJmQ9WVdrOWVUaDBTVU5HTTJNbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD1mMg--","secret":"65d9b487477c25d1a99c2d04a9b6ebe43aa8e59e"}},"AOL":{"enabled":"1"},"Google":{"enabled":"1","keys":{"id":"331300171509-eq3n281r8gj9foehiebqvkdvk4729im8.apps.googleusercontent.com","secret":"YZx_4lMMpaqZ9mnB-BhwCfV4"}},"Facebook":{"enabled":"1","keys":{"id":"367576600118660","secret":"6c6caed52da16538a163ce90e27aa613"}},"Twitter":{"enabled":"1","keys":{"key":"AH4qoEAAC3a19jdkzhSDArGaO","secret":"TrIY0JGmkU4dO0m1yZlzizmBBxdf4cI7gOvcrv5TVwsssYMw8w"}},"Live":{"enabled":"0","keys":{"id":"","secret":""}},"MySpace":{"enabled":"0","keys":{"key":"","secret":""}},"LinkedIn":{"enabled":"1","keys":{"key":"77tkekg8thutih","secret":"8IpwzPcSsMdJILDs"}},"Foursquare":{"enabled":"1","keys":{"id":"4GO5TRBR1AWZWPSFF5DZKJLR0JO55X3Q0LASLKIZOF2ZAQJE","secret":"CJOYVJ10HK1DIR12A05UPR5OD33T0UOQGRT3BL54NCXEVCWO"}}},"base_url":"http:\/\/reoc.lo\/social\/login\/1","debug_file":"\/var\/www\/reoc\/oc\/vendor\/hybridauth\/logs.txt"}');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

// delete all cache
$I->amOnPage('/oc-panel/tools/cache?force=1');
$I->see('All cache deleted');

$I->activate_theme('splash');

$I->amOnPage('/');
$I->click('Logout');

$I->amOnPage('/oc-panel/auth/login');
$I->seeElement('.zocial.openid.social-btn');
$I->seeElement('.zocial.yahoo.social-btn');
$I->seeElement('.zocial.aol.social-btn');
$I->seeElement('.zocial.google.social-btn');
$I->seeElement('.zocial.facebook.social-btn');
$I->seeElement('.zocial.twitter.social-btn');
$I->seeElement('.zocial.linkedin.social-btn');
$I->seeElement('.zocial.foursquare.social-btn');

$I->login_admin();

$I->activate_theme('default');

$I->amOnPage('/oc-panel/Config/update/config');
$I->fillField('#formorm_config_value','{"debug_mode":"0","providers":{"OpenID":{"enabled":"0"},"Yahoo":{"enabled":"0","keys":{"id":"--","secret":""}},"AOL":{"enabled":"0"},"Google":{"enabled":"0","keys":{"id":"","secret":""}},"Facebook":{"enabled":"0","keys":{"id":"","secret":""}},"Twitter":{"enabled":"0","keys":{"key":"","secret":""}},"Live":{"enabled":"0","keys":{"id":"","secret":""}},"MySpace":{"enabled":"0","keys":{"key":"","secret":""}},"LinkedIn":{"enabled":"0","keys":{"key":"","secret":""}},"Foursquare":{"enabled":"0","keys":{"id":"","secret":""}}},"base_url":"http:\/\/reoc.lo\/social\/login\/1","debug_file":"\/var\/www\/reoc\/oc\/vendor\/hybridauth\/logs.txt"}');
$I->click('button[type="submit"]');
$I->see('Item updated. Please to see the changes delete the cache');

$I->amOnPage('/info.json');
$I->dontSee('Splash');