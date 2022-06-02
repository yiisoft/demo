<?php

declare(strict_types=1);

namespace App\Tests\Cli;

use App\Tests\CliTester;
use Yiisoft\Yii\Console\ExitCode;

final class ConsoleCest
{
    public function testCommandYii(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command);
        $I->seeInShellOutput('Yii Console');
    }

    public function testCommandFixtureAdd(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command . ' fixture/add');
        $I->seeResultCodeIs(ExitCode::OK);
    }

    public function testCommandListCommand(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command . ' list');
        $I->seeResultCodeIs(ExitCode::OK);
    }
}
