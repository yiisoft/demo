<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\VersionProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Testing\FunctionalTester;

final class IndexControllerTest extends TestCase
{
    private ?FunctionalTester $tester;

    protected function setUp(): void
    {
        $this->tester = new FunctionalTester();
    }

    public function testGetIndex(): void
    {
        $method = 'GET';
        $url = '/';

        $this->tester->bootstrapApplication(dirname(__DIR__, 2));
        $response = $this->tester->doRequest($method, $url);

        $this->assertEquals(
            [
                'status' => 'success',
                'error_message' => '',
                'error_code' => null,
                'data' => ['version' => '3.0', 'author' => 'yiisoft'],
            ],
            $response->getContentAsJson()
        );
    }

    public function testGetIndexMockVersion(): void
    {
        $method = 'GET';
        $url = '/';

        $this->tester->bootstrapApplication(dirname(__DIR__, 2));

        $this->tester->mockService(VersionProvider::class, new VersionProvider('3.0.0'));

        $response = $this->tester->doRequest($method, $url);

        $this->assertEquals(
            [
                'status' => 'success',
                'error_message' => '',
                'error_code' => null,
                'data' => ['version' => '3.0.0', 'author' => 'yiisoft'],
            ],
            $response->getContentAsJson()
        );
    }
}
