<?php

declare(strict_types=1);

namespace App\Tests\Functional;


use App\Tests\Support\FunctionalTester;

final class IndexControllerCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/');
    }

    public function testGetIndex(FunctionalTester $I)
    {
        $I->see('Hello, everyone');
    }
}
