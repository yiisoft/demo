<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\FunctionalTester;

final class ContactCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/contact');
    }

    public function openContactPage(FunctionalTester $I)
    {
        $I->wantTo('ensure that contact page works');
        $I->seeInField('contact-button', 'Submit');
    }

    public function submitEmptyForm(FunctionalTester $I)
    {
        $I->submitForm('#form-contact', []);
        $I->expectTo('see validations errors');
        $I->see('Value cannot be blank.');
    }

    public function submitFormWithIncorrectEmail(FunctionalTester $I)
    {
        $I->submitForm('#form-contact', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester.email',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
            'ContactForm[verifyCode]' => 'testme',
        ]);
        $I->expectTo('see that email address is wrong');
        $I->see('This value is not a valid email address.');
    }

    public function submitFormSuccessfully(FunctionalTester $I)
    {
        $I->submitForm('#form-contact', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester@example.com',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
        ]);
        $I->see("Thank you for contacting us, we'll get in touch with you as soon as possible.");
    }
}
