<p align="center">
    <a href="http://www.yiiframework.com/" target="_blank">
        <img src="https://www.yiiframework.com/files/logo/yii.png" width="400" alt="Yii Framework" />
    </a>
    <h1 align="center">Yii Framework Demo Project</h1>
    <br>
</p>

[Yii Framework] is a modern framework designed to be a solid foundation for your PHP application.

It's intended to show and test all Yii features.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii-demo/v/stable.png)](https://packagist.org/packages/yiisoft/yii-demo)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii-demo/downloads.png)](https://packagist.org/packages/yiisoft/yii-demo)
[![build](https://github.com/yiisoft/yii-demo/workflows/build/badge.svg)](https://github.com/yiisoft/yii-demo/actions)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/yii-demo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-demo/?branch=master)
[![static analysis](https://github.com/yiisoft/yii-demo/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/yii-demo/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/yii-demo/coverage.svg)](https://shepherd.dev/github/yiisoft/yii-demo)

## Installation

You'll need at least PHP 7.4.

1. Clone this repository.
2. Configure `config/params.php`. You can skip this step.
3. Run `composer install` in project root directory.
4. Run `./vendor/bin/yii serve` or start your web-server setting up `public` directory as webroot.
5. Go to index page. Cycle ORM will create tables, indexes and relations automatically in the configured DB.
  If you want to disable this behavior then comment out line with `Generator\SyncTables::class` in the `config/params.php`.
  In this case you should create migrations to sync changes of entities with DB.
 6. Run `./vendor/bin/yii fixture/add 20` to create some random data.

## Console

Console works out of the box and could be executed with `./vendor/bin/yii`.

Some commands:

```bash
user/create <login> <password>
fixture/add [count]
```

In order to register your own commands, add them to `console/params.php`, `console` → `commands` section.

## Web application

In order to run web application either built-in web server could be used by running `./vendor/bin/yii serve` or a
real web server could be pointed to `/public/index.php`.

More routes could be added by editing `src/Factory/AppRouterFactory`.

## Testing

The template comes with ready to use [Codeception](https://codeception.com/) configuration.
In order to execute tests run:

```
composer run serve > ./runtime/yii.log 2>&1 &
vendor/bin/codecept run
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

### Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)

## License

The Yii Framework Demo Project is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
