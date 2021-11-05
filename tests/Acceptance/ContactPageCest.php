<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

final class ContactPageCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/contact');
    }

    public function contactPageWorks(AcceptanceTester $I)
    {
        $I->wantTo('ensure that contact page works');
        $I->seeInField('contact-button', 'Submit');
    }

    public function contactFormCanBeSubmitted(AcceptanceTester $I)
    {
        $I->amGoingTo('submit contact form with correct data');
        $I->fillField('#contactform-name', 'tester');
        $I->fillField('#contactform-email', 'tester@example.com');
        $I->fillField('#contactform-subject', 'test subject');
        $I->fillField('#contactform-body', 'test content');

        $I->click('Submit');

        $I->see("Thank you for contacting us, we'll get in touch with you as soon as possible.");
    }
}
