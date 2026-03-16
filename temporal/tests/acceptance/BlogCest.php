<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;
use Codeception\Util\HttpCode;

final class BlogCest
{
    public function index(AcceptanceTester $I): void
    {
        $I->sendGET(
            '/blog/',
            [
                'page' => 2,
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'success',
                'error_message' => '',
                'error_code' => null,
                'data' => [
                    'paginator' => [
                        'pageSize' => 10,
                        'currentPage' => 2,
                        'totalPages' => 2,
                    ],
                    'posts' => [
                        [
                            'id' => 11,
                            'title' => 'Eveniet est nam sapiente odit architecto et.',
                        ],
                    ],
                ],
            ]
        );
    }

    public function view(AcceptanceTester $I): void
    {
        $I->sendGET('/blog/11');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'success',
                'error_message' => '',
                'error_code' => null,
                'data' => [
                    'post' => [
                        'id' => 11,
                        'title' => 'Eveniet est nam sapiente odit architecto et.',
                    ],
                ],
            ]
        );
    }
}
