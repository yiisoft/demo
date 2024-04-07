<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Framework API Demo Project</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/demo-api/v/stable.png)](https://packagist.org/packages/yiisoft/demo-api)
[![Total Downloads](https://poser.pugx.org/yiisoft/demo-api/downloads.png)](https://packagist.org/packages/yiisoft/demo-api)
[![Build status](https://github.com/yiisoft/demo-api/workflows/build/badge.svg)](https://github.com/yiisoft/demo-api/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/demo-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/demo-api/?branch=master)
[![static analysis](https://github.com/yiisoft/demo-api/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/demo-api/actions?query=workflow%3A%22static+analysis%22)

API Demo application for Yii 3.

## Installation

Install docker:

```bash
docker-compose up -d
```

Enter into the container:

```bash
docker exec -it yii-php bash
```

Install packages:

```bash
composer install
```

Change ownership of the app directory to web group:

```bash
chown -R :www-data .
```

Usually the application is available at http://localhost:8080.

Authorization is performed via the `X-Api-Key` header.

## API documentation

API documentation is available at `/docs`. It is built from OpenAPI attributes (`#[OA\ ... ]`).
See [Swagger-PHP documentation](https://zircote.github.io/swagger-php/Getting-started.html#write-annotations) for details
on how to annotate your code.

## Codeception testing

```php
./vendor/bin/codecept run
```


## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```php
./vendor/bin/psalm
```
