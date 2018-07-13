Add multi-domain support in Laravel
===================================

## Let's get started

First of all, include this library into your Laravel projet dependencies,
using the following command:

```sh
composer require ludovicm67/laravel-multidomain`
```

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

One special case: if the asked domain is starting with `api.` and if in the
configuration file there are only a version without the `api.`, this last
one will be used.

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

In our example, where we wanted to have a specific database configuration
for each domain, you just have to update your `config/database.php` file, to
make it similar to something like:

```php
<?php

use \ludovicm67\Laravel\Multidomain\Configuration;
use \ludovicm67\Laravel\Multidomain\ConfigurationObject;

// default configuration without all comments
$databaseConfiguration = [
  'default' => env('DB_CONNECTION', 'mysql'),
  // removed 'connections' key here
  'migrations' => 'migrations',
  'redis' => [
    'client' => 'predis',
    'default' => [
      'host' => env('REDIS_HOST', '127.0.0.1'),
      'password' => env('REDIS_PASSWORD', null),
      'port' => env('REDIS_PORT', 6379),
      'database' => 0,
    ],
  ],
];

// get configuration
$config = Configuration::getInstance();
$globalConf = $config->get();
$domainConf = $config->getDomain();
$databaseConfiguration['connections'] = []; // empty array
$databaseConfiguration['connections']['mysql'] = [
  'driver' => 'mysql',
  'database' => ''
]; // default to prevent some errors

// add default database connection if we have a domain
if (!is_null($domainConf)) {
  $databaseConf = $domainConf->get('database');
  if (!is_null($databaseConf) && is_object($databaseConf)) {
    // we create the default database connection using our specified domain
    $databaseConfiguration['connections']['mysql'] = [
      'driver' => 'mysql',
      'host' => $databaseConf->get('hostname'),
      'port' => '3306',
      'database' => $databaseConf->get('database'),
      'username' => $databaseConf->get('username'),
      'password' => $databaseConf->get('password'),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'strict' => true,
      'engine' => null,
    ];
  }
}

// append database configuration for other domains (for migrations for example)
$supportedDomains = $globalConf->get('supported_domains');
if (!empty($supportedDomains)) $supportedDomains = $supportedDomains->get();
if (!empty($supportedDomains)) {
  foreach ($supportedDomains as $domain => $conf) {
    $databaseConf = (new ConfigurationObject($conf))->get('database');
    if (!is_null($databaseConf) && is_object($databaseConf)) {
      $databaseConfiguration['connections'][$domain] = [
        'driver' => 'mysql',
        'host' => $databaseConf->get('hostname'),
        'port' => '3306',
        'database' => $databaseConf->get('database'),
        'username' => $databaseConf->get('username'),
        'password' => $databaseConf->get('password'),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
      ];
    }
  }
}

return $databaseConfiguration;

```

And to run migrations, just run the following command:

```sh
php artisan migrate --database=YOUR_DOMAIN
```

In our example, it will be:

```sh
php artisan migrate --database=localhost
php artisan migrate --database=amazing.dev
```

And that's it! :wink:
