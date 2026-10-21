OkayCMS v4.5.3
----------------------

OkayCMS is a PHP CMS for creating backend and frontend applications.

 - Homepage:        [https://okay-cms.com](https://okay-cms.com)
 - Documentation:   [https://github.com/OkayCMS/OkayCMS/tree/master/docs](https://github.com/OkayCMS/OkayCMS/tree/master/docs)
 - Repository:      [https://github.com/OkayCMS/OkayCMS](https://github.com/OkayCMS/OkayCMS)

PHP versions and Composer lock files
----------------------

Supported PHP: 7.4, 8.1, 8.2, 8.3, 8.4, 8.5.

| File                  | PHP                          |
|-----------------------|------------------------------|
| `composer.lock`       | 8.5 (same as composer.php85.lock) |
| `composer.php85.lock` | 8.5                          |
| `composer.php84.lock` | 8.4                          |
| `composer.php83.lock` | 8.3                          |

To install dependencies for a specific PHP version:

```shell
cp composer.php84.lock composer.lock
composer install
git checkout composer.lock
```

There are no lock files for 7.4, 8.1 and 8.2, so use `composer update` there and don't commit the resulting `composer.lock`.
PHP 7.4 and 8.1 are end-of-life and no longer get security fixes, so don't use them in production.

OkayCMS is released under the LGPL license.

Copyright 2015-2026 OkayCMS