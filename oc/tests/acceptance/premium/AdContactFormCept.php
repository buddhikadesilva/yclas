<?php 
$I = new AcceptanceTester($scenario);
$I->am('a visitor');
$I->amGoingTo('use the ad contact form');

// Messaging system is ON by default, so I am getting the login form instead. 
$I->amOnPage('/jobs/title-for-the-ad.html');
$I->see('Send Message','a');









