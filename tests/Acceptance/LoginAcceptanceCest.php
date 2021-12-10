<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

final class LoginAcceptanceCest
{
    public function testLoginPage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->expectTo('see log in page.');
        $I->see('Login');
    }

    public function testLoginEmptyDataTest(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-login', '');
        $I->fillField('#login-password', '');

        $I->click('Submit', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank');
        $I->see('Value cannot be blank');
        $I->seeInField('login-button', 'Submit');
    }

    public function testLoginSubmitFormWrongDataUsername(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin1');
        $I->fillField('#login-password', '123456');
        $I->checkOption('#login-rememberme');

        $I->click('Submit', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Invalid login or password');
        $I->seeInField('login-button', 'Submit');
    }

    public function testLoginSubmitFormWrongDataPassword(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the login page.');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '1');
        $I->checkOption('#login-rememberme');

        $I->click('Submit', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Invalid login or password');
        $I->seeInField('login-button', 'Submit');
    }

    /**
     * @depends App\Tests\Acceptance\SignupAcceptanceCest:testRegisterSuccess
     */
    public function testLoginUsernameSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-login', 'admin');
        $I->fillField('#login-password', '123456');
        $I->checkOption('#login-rememberme');

        $I->click('Submit', '#loginForm');

        $I->expectTo('see logged index page.');
        $I->dontSeeLink('login');

        $I->click('Logout (admin)');

        $I->expectTo('no see link logout.');
        $I->dontSeeLink('logout');
    }
}
