<p align="center">
    <a href="http://www.yiiframework.com/" target="_blank">
        <img src="https://www.yiiframework.com/image/yii_logo_light.png" width="400" alt="Yii Framework" />
    </a>
    <h1 align="center">Yii Framework Demo Project</h1>
    <h5 align="center">...with Invoicing System</h5>
    <br>
</p>

May 10 ..\config\packages\yiisoft\yii-cycle\params adjusted. Using default 'synctable' settings.

May 11 .. Useful read on annotations here [https://php-annotations.readthedocs.io/en/latest/index.html](https://php-annotations.readthedocs.io/en/latest/index.html)

May 12 ..src\Entity\Invoice\Client.php and src\Invoice created with annotations and now database is being built automatically. Use yii cycle/schema to
build and include new client table. Useful read: https://cycle-orm.dev/docs/intro-quick-start.

May 14 ..https://github.com/cycle/annotated/blob/master/resources/stubs/Column.php 
1. Types: 'primary', 'bigPrimary', 'enum', 'boolean', 'integer', 'tinyInteger', 'bigInteger', 'string', 'text', 'tinyText', 'longText', 'double', 'float', 'decimal', 'datetime', 'date', 'time', 'timestamp', 'binary', 'tinyBinary', 'longBinary', 'json'
2. Entity 'Clients' and 'Settings' created manually.  

May 15..Useful code:

Logger for emergency, warning, info, error, debug (../vendor/yiisoft/log/README.MD) eg.

````
    public function __construct()
	{
	    $this->_logger = new \Yiisoft\Log\Logger();
            $this->_logger->info('Language Class Initialized');            
	}
````

Aliases (../vendor/yiisoft/aliases/README.MD) eg.

````
    $aliases = new \Yiisoft\Aliases\Aliases(['@invoice' => __DIR__ . '/src/invoice', '@language' => '@invoice/language']);
    $path = $aliases->get('@language');
````

May 16..Useful code: rbac.. Assign the 'admin' role to first signed up user => assignment.php generated under ../resources/rbac. 

````
    yii user/assignRole admin 1
````
May 19..Client Entity created. 

1. Ensure _form 'id' and 'name' values eg. client_name correspond to Entity @column and Database tables fields. ie Use field names consistently
   through Entity, Annotations.
1. Ensure initialization in instantiation area ie. BEFORE construct and IN construct.
1. Ensure Client table structure replicates Invoiceplane's Client table structure including birthdate date type. 

May 20..Client Entity testing commencing. Birthdate Tips.
1. Annotations above function read by Cycle. For newbies ...they are not comments.
1. ```` use \DateTime; ```` before Annotations. Don't forget backslash to indicate DateTime is a php class not in current Namespace.
1. mySql type DATE in database and 'date' in annotation below. ie. ````* @Column(type="date", nullable=true)````
1. Question mark before DateTime in function allows null value which we want since Date is not compulsory. ie. can be empty.
1. Ensure question mark before DateTime even in ````public function getClient_birthdate() : ?\DateTime  ```` and 
   ````
   public function setClient_birthdate(?\DateTime $client_birthdate): void
   ```  

...src/Invoice/Entity/Client.php...
````
     /**
     * @Column(type="date", nullable=true)
     */
    private ?DateTime $client_birthdate = null;    
````
1. Question mark before ?\DateTime allows for null value. Use consistently in function declaration as well as seen below.  
...src/Invoice/Entity/Client.php **and below**  
...src/Invoice/Client/ClientForm.php
````
    public function getClient_birthdate(): ?\DateTime
    {
        if (isset($this->client_birthdate) && !empty($this->client_birthdate)){return new DateTime($this->client_birthdate);}
        else return $this->client_birthdate = null;        
    }
````


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
2. Run `composer update` in project root directory.
3. Run `./yii serve` (on Windows `yii serve`). The application will be started on http://localhost:8080/.
4. Go to index page. Cycle ORM will create tables, indexes and relations automatically in the configured DB.
   If you want to disable this behavior then comment out line with `Generator\SyncTables::class` in the `config/packges/yiisoft/yii-cycle/params.php`.
   In this case you should create migrations to sync changes of entities with DB.
5. Run `./yii fixture/add 20` to create some random data.

## Console

Console works out of the box and could be executed with `./yii`.

Some commands:

```bash
user/create <login> <password>
fixture/add [count]
```

In order to register your own commands, add them to `console/params.php`, `console` → `commands` section.

## Web application

In order to run web application either built-in web server could be used by running `./yii serve` or a
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
