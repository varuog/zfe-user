# zfe-user

## Description
Commong User handling task for zend expressive 2 application. currently supports MongoDB based datatbase. has option for
HTML, JSON or JSON-API payload.

## Composer Install
```bash
$composer require varuog/zfe-user
```

Copy file zfe-user.global.php from instllation directory  to config\autoload\zfe-user.global.php.dist and
rename it to zfe-user.global.php

Add this code block to `config\atuoload\dependencies` under dependency keys
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

## Notice
**It is under active development. (WIP) Not suitable for production.**
