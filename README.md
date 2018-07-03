Add multi-domain support in Laravel
===================================

## Let's get started

First of all, include this library into your Laravel projet dependencies.

Create a file called `config.yml` at the root of your project, with something
like:

```yml
fallback_url: http://localhost/
supported_domains:
  localhost:
    site_name: Localhost
    database:
      hostname: localhost
      username: root
      password:
      database: db
  amazing.dev:
    site_name: Amazing!
    database:
      hostname: localhost
      username: amazing
      password: wow
      database: amazing
```

If the current hostname is not in the `supported_domains` list, the app will
redirect to the `fallback_url`.

You can add all properties as you wish; here we will for example see how to
have a different database for each domain.

To get started, update the `bootstrap/app.php` file to load the configuration,
as follow:

```php
<?php

// create the application
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

// load all required configuration for multi domain support
\ludovicm67\Laravel\Multidomain\Configuration::getInstance(
    base_path('config.yml')
);

// ... the rest of the file will be the same so keep it
```

If you want to have access to some properties, you can now add everywhere you
want the following:

```php
<?php
// ...
use \ludovicm67\Laravel\Multidomain\Configuration;

// ...

$config = Configuration::getInstance(); // here we will get our instance
$config->get(); // to get access to the whole configuration
$config->domain(); // to get access to the current domain configuration
```
