<?php
namespace As_Test1_User;
use \AcceptanceTester;
use \Common;

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

    /**
     * GIVEN See my dashboard content
     * WHEN I login correctly
     * THEN I should see the text "User Management" and should see the text "Dear test1"
     *
     * Scenario 10.1.2
     * @before login
     */
    public function seeMyDashboardContent(AcceptanceTester $I)
    {
        $I->canSeeInCurrentUrl('/admin/dashboard');
        $I->canSee('Dear test1');
        //$I->canSee('User Management');
    }
}
