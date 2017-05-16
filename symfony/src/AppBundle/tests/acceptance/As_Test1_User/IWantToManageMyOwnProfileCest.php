<?php
namespace As_Test1_User;
use \AcceptanceTester;
use \Common;

class IWantToManageMyOwnProfileCest
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

    /**
     * GIVEN Show my profile
     * WHEN I go to "/admin/?action=show&entity=User&id=2"
     * THEN I should see test1@songbird.app and an Image field
     *
     * Scenario 10.4.1
     * @before login
     */
    public function showMyProfile(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/?action=show&entity=User&id=2');
        $I->canSee('test1@songbird.app');
        $I->canSee('Email');
        // get original image
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // can see new image
        $I->waitForElement('//img[contains(@src, "'.$imagePath.'")]');
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');
    }

    /**
     * GIVEN Hid uneditable fields
     * WHEN I go to "/admin/?action=edit&entity=User&id=2"
     * THEN I should not see enabled and roles fields
     *
     * Scenario 10.4.2
     * @before login
     */
    public function hidUneditableFields(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->cantSee('Enabled');
        $I->cantSee('Roles');
    }

    /**
     * GIVEN Delete and Add profile image
     * WHEN I go to edit profile page And delete profile image and add a new image
     * THEN I should see an empty profile, previous profile image gone and then a new one appearing in the file system
     *
     * Scenario 10.4.5
     * @before login
     */
    public function deleteAndAddProfileImage(AcceptanceTester $I)
    {
        // FIle upload doesn't work for phantomjs, will relook this at a later stage
        return;

        // get original image
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // check image available
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');
        $I->click('test1');
        $I->click('Edit');
        $I->click('//input[@id="user_imageFile_delete"]');
        // submit form
        $I->click('//button[@type="submit"]');
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        // can see empty images
        $I->canSee('Empty');
        // check that image is not there
        $I->cantSeeFileFound($imagePath, '../../web/uploads/profiles');
        // now revert changes
        $I->click('test1');
        $I->click('Edit');
        $I->waitForElementVisible('//input[@type="file"]');
        $I->attachFile('//input[@id="user_imageFile_file"]', 'test_profile.jpg');
        // update
        $I->click('//button[@type="submit"]');
        // get image from db
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // check image available
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');
    }

}
