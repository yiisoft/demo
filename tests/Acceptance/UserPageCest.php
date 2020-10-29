<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

final class UserPageCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->wantTo('user page works.');
        $I->amOnPage('/user');
    }

    public function testUserPage(AcceptanceTester $I): void
    {
        $I->expectTo('see user page.');
        $I->seeLink('API v1 Info');
        $I->seeLink('API v2 Info');
        $I->seeLink('API Users List Data');
    }
}
