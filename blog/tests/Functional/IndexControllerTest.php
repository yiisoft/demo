<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Testing\FunctionalTester;

final class IndexControllerTest extends TestCase
{
    private ?FunctionalTester $tester;

    protected function setUp(): void
    {
        $this->tester = new FunctionalTester();
    }

    public function testGetIndex()
    {
        $method = 'GET';
        $url = '/';

        $this->tester->bootstrapApplication(dirname(__DIR__, 2));
        $response = $this->tester->doRequest($method, $url);

        $this->assertStringContainsString('Hello, everyone', $response->getContent());
    }
}
