<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

final class SignupAcceptanceCest
{
    public function testSignupPage(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/signup');

        $I->expectTo('see register page.');
        $I->see('Signup');
    }

    public function testRegisterSuccess(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/signup');

        $I->fillField('#signup-login', 'admin');
        $I->fillField('#signup-password', '123456');
        $I->fillField('#signup-passwordverify', '123456');

        $I->click('Submit', '#signupForm');

        $I->expectTo('see register success message.');
        $I->see('Hello, everyone!');
    }

    public function testRegisterEmptyData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/signup');

        $I->fillField('#signup-login', '');
        $I->fillField('#signup-password', '');
        $I->fillField('#signup-passwordverify', '');

        $I->click('Submit', '#signupForm');

        $I->expectTo('see registration register validation.');
        $I->see('Value cannot be blank');
        $I->see('Value cannot be blank');
        $I->see('Value cannot be blank');
        $I->seeInField('register-button', 'Submit');
    }

    public function testRegisterUsernameExistData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/signup');

        $I->fillField('#signup-login', 'admin');
        $I->fillField('#signup-password', '123456');
        $I->fillField('#signup-passwordverify', '123456');

        $I->click('Submit', '#signupForm');

        $I->expectTo('see registration register validation.');
        $I->see('User with this login already exists');
        $I->seeInField('register-button', 'Submit');
    }

    public function testRegisterWrongPassword(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the register page.');
        $I->amOnPage('/signup');

        $I->fillField('#signup-login', 'admin1');
        $I->fillField('#signup-password', '123456');
        $I->fillField('#signup-passwordverify', '12345');

        $I->click('Submit', '#signupForm');

        $I->expectTo('see registration register validation.');
        $I->see('Passwords do not match');
    }
}
