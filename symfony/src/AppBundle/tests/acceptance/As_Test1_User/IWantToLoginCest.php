<?php
namespace As_Test1_User;
use \AcceptanceTester;

class IWantToLoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function login(AcceptanceTester $I)
    {
        Common::login($I, TEST1_USERNAME, TEST1_PASSWORD);
    }

    public function wrongLoginCredentials(AcceptanceTester $I)
    {
        $I->amOnPage('/admin');
        $I->fillField('//input[contains(@id, "username")]', TEST1_USERNAME);
        $I->fillField('//input[contains(@id, "password")]', TEST2_PASSWORD);
        $I->click('//input[@type="submit"]');
        //$I->amOnPage('/login');
        $I->canSeeInCurrentUrl('login');

        $I->waitForElementVisible('//input[@id="username"]');
        $I->waitForElementVisible('//input[@id="password"]');
        //$I->canSee('Invalid credentials');
        $I->waitForText('Invalid credentials');
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    }
}
