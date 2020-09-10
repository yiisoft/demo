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
[![Build Status](https://travis-ci.com/yiisoft/yii-demo.svg?branch=master)](https://travis-ci.com/yiisoft/yii-demo)

## Installation

You'll need PHP 7.4. Additionally, [NodeJs](https://nodejs.org/en/) is used in this repository to fetch assets, so it
should be installed.

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
