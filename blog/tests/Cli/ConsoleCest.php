<?php

declare(strict_types=1);

namespace App\Tests\Cli;

use App\Tests\Support\CliTester;
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

    public function testCommandUserCreateSuccessCommand(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command . ' user/create user create123456');
        $I->seeResultCodeIs(ExitCode::OK);
    }

    /**
     * Clear all data created with testCommandFixtureAdd().
     * Clearing database prevents from getting errors during multiple continuous testing with other test,
     * what are based on empty database (eg, BlogPageCest).
     */
    public function testCommandCycleSchemaClear(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command . ' fixture/schema/clear');
        $I->seeResultCodeIs(0);
    }
}
