<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

final class CommentPageCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->wantTo('comment page works.');
        $I->amOnPage('/blog/comments/');
    }

    public function testCommentPage(AcceptanceTester $I): void
    {
        $I->expectTo('see comment page.');
        $I->see('Comments Feed');
    }
}
