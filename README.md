# zfe-user

## Notice
**It is under active development. (WIP) Not suitable for production.**


## Description
Commong User handling task for zend expressive 2 application. currently supports 
MongoDB based database, JSON-API payload. Does not includes any view templates 
or view helpers. Its end points meant to be consumed by API clients. Though it 
is easily possible to compose a view from `UserService` and use customized view.


## Features
1. User is able to register and login
2. User can reset their password and email with mail verification. auto discarded
reset tokens.
3. Customizable mail template for notifying user and admin
4. User can be fetched via url
5. Access token based authentication

## Future plan
1. Currently it only supports mongodb. would add doctrine ORM (mysql) and zend-db
2. Currently it only supports json-api payload. Would add other payload.
3. Add event hook for all process

## Installation
```bash
$composer require varuog/zfe-user
```

Copy file `zfe-user.global.php` from instllation directory  to config\autoload\zfe-user.global.php.dist and
rename it to zfe-user.global.php

Copy `data\language` folder from zfe-user and paste it to application data directory
Create other directory `data\proxies`, `data\hydrators`, `data\document`

Copy `template` folder from zfe-user and paste to application template folder
Add this code block to `config\atuoload\dependencies.global.php` under dependency keys
```php
 'dependencies' => [
            /**
             * Copy this block
             */
            'delegators' => [
           
            Application::class => [
                \ZfeUser\RouteProvider::class
            ],
        ],
]
```

## Credits
Abstract Factories for all action are based on @xtreamwayz
https://xtreamwayz.com/blog/2015-12-30-psr7-abstract-action-factory-one-for-all