<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;
use Codeception\Util\HttpCode;

final class BlogCest
{
    public function create(AcceptanceTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader(
            'X-Api-Key',
            'lev1ZsWCzqrMlXRI2sT8h4ApYpSgBMl1xf6D4bCRtiKtDqw6JN36yLznargilQ_rEJz9zTfcUxm53PLODCToF9gGin38Rd4NkhQPOVeH5VvZvBaQlUg64E6icNCubiAv'
        );

        $I->sendPOST(
            '/blog/',
            [
                'title' => 'test title',
                'text' => 'test text',
                'status' => 0,
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'success',
                'error_message' => '',
                'error_code' => null,
                'data' => null,
            ]
        );

        $I->seeInDatabase(
            'post',
            [
                'title' => 'test title',
                'content' => 'test text',
                'status' => 0,
            ]
        );
    }

    public function createBadParams(AcceptanceTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader(
            'X-Api-Key',
            'lev1ZsWCzqrMlXRI2sT8h4ApYpSgBMl1xf6D4bCRtiKtDqw6JN36yLznargilQ_rEJz9zTfcUxm53PLODCToF9gGin38Rd4NkhQPOVeH5VvZvBaQlUg64E6icNCubiAv'
        );

        $I->sendPOST(
            '/blog/',
            [
                'title' => 'test title',
                'status' => 0,
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'failed',
                'error_message' => 'Text not passed.',
                'error_code' => 400,
                'data' => null,
            ]
        );

        $I->dontSeeInDatabase(
            'post',
            [
                'title' => 'test title',
                'status' => 0,
            ]
        );
    }

    public function createBadAuth(AcceptanceTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            '/blog/',
            [
                'title' => 'test title',
                'text' => 'test text',
                'status' => 0,
            ]
        );
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'failed',
                'error_message' => 'Unauthorised request',
                'error_code' => HttpCode::UNAUTHORIZED,
                'data' => null,
            ]
        );
    }

    public function update(AcceptanceTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader(
            'X-Api-Key',
            'lev1ZsWCzqrMlXRI2sT8h4ApYpSgBMl1xf6D4bCRtiKtDqw6JN36yLznargilQ_rEJz9zTfcUxm53PLODCToF9gGin38Rd4NkhQPOVeH5VvZvBaQlUg64E6icNCubiAv'
        );

        $I->sendPUT(
            '/blog/1',
            [
                'title' => 'test title',
                'text' => 'test text',
                'status' => 0,
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'success',
                'error_message' => '',
                'error_code' => null,
                'data' => null,
            ]
        );

        $I->seeInDatabase(
            'post',
            [
                'id' => 1,
                'title' => 'test title',
                'content' => 'test text',
                'status' => 0,
            ]
        );
    }

    public function updateBadAuth(AcceptanceTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT(
            '/blog/1',
            [
                'title' => 'test title',
                'text' => 'test text',
                'status' => 0,
            ]
        );
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'status' => 'failed',
                'error_message' => 'Unauthorised request',
                'error_code' => HttpCode::UNAUTHORIZED,
                'data' => null,
            ]
        );
    }

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
