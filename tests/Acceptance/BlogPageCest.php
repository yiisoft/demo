<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

final class BlogPageCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->wantTo('blog page works.');
        $I->amOnPage('/blog');
    }

    public function testBlogPage(AcceptanceTester $I): void
    {
        $I->expectTo('see blog page.');
        $I->see('Blog');
        $I->see('No records');
    }
}
