<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    public function login_admin()
    {
    	$I = $this;
    	$I->amOnPage('/oc-panel/auth/login');
		$I->fillField('email','admin@gmail.com');
		$I->fillField('password','1234');
		$I->click('auth_redirect');
		$I->amOnPage('/oc-panel/');
		$I->see('welcome admin');
    }

    public function activate_theme($theme)
    {
        $I = $this;
        $I->wantTo('activate '. $theme .' theme again');
        $I->amOnPage('/oc-panel/Config/update/theme');
        $I->fillField('#formorm_config_value',$theme);
        $I->click('button[type="submit"]');
    }
}
