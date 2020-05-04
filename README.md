OdooApiBundle
=============

[![Build Status](https://travis-ci.org/Ang3/odoo-api-bundle.svg?branch=master)](https://travis-ci.org/Ang3/odoo-api-bundle) 
[![Latest Stable Version](https://poser.pugx.org/ang3/odoo-api-bundle/v/stable)](https://packagist.org/packages/ang3/odoo-api-bundle) 
[![Latest Unstable Version](https://poser.pugx.org/ang3/odoo-api-bundle/v/unstable)](https://packagist.org/packages/ang3/odoo-api-bundle) 
[![Total Downloads](https://poser.pugx.org/ang3/odoo-api-bundle/downloads)](https://packagist.org/packages/ang3/odoo-api-bundle)

Symfony integration of the package 
[ang3/php-odoo-api-client](https://packagist.org/packages/ang3/php-odoo-api-client) - 
Please read the [documentation of the client](https://github.com/Ang3/php-odoo-api-client) 
to know how to use it.

This bundle helps you to manage your Odoo connections by providing a registry 
and your clients as services.

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require ang3/odoo-api-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

**This step is done automatically on symfony ```>=4.0```**

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
  public function registerBundles()
  {
    $bundles = array(
      // ...
      new Ang3\Bundle\OdooApiBundle\Ang3OdooApiBundle(),
    );

    // ...
  }

  // ...
}
```

Step 3: Configure your app
--------------------------

Depends on your symfony version, enable the configuration of the bundle:

```yaml
# app/config/config.yml or config/packages/ang3_odoo_api.yaml
ang3_odoo_api:
  default_connection: default
  connections:
    default:
      url: <database_url>
      database: <database_name>
      user: <username>
      password: <password>
```

The parameter ```default_connection``` is used to define the default connection to use.

Usage
=====

First, configure your connections in the package configuration file. 
That should be done in step 3 of the installation section.

Registry
--------

If you want to work with all your configured clients, then you may want to get the *registry*. 
It stores all configured clients by connection name. You can get it by dependency injection:

```php
use Ang3\Bundle\OdooApiBundle\ClientRegistry;

class MyService
{
    private $clientRegistry;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }
}
```

The registry contains three useful methods:
- ```public function set(string $connectionName, Client $client): self``` Set a client by connection name.
- ```public function get(string $connectionName): Client``` Get the client of a connection. A ```\LogicException``` is thrown if the connection was not found.
- ```public function has(string $connectionName): bool``` Check if a connection exists by name.

If you don't use autowiring, you must pass the service as argument of your service:

```yaml
# app/config/services.yml or config/services.yaml
# ...
MyClass:
    arguments:
        $clientRegistry: '@ang3_odoo_api.client_registry'
```

Clients
-------

It could be useful to get a client directly without working with the registry.

For example, the get the default client by autowiring, use the argument 
```Ang3\Component\Odoo\Client $client```:

```php
use Ang3\Component\Odoo\Client;

class MyService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
```

Run the command ```php bin/console debug:autowiring ApiClient``` to get the list of autowired clients.

- If the connection name is ```acme```, then the autowiring argument is 
```Ang3\Component\Odoo\Client $acmeClient```

Finally, if you don't use autowiring, you must pass the service as argument of your service:

```yaml
# app/config/services.yml or config/services.yaml
# ...
MyClass:
    arguments:
        $clientRegistry: '@ang3_odoo_api.client.<connection_name>' # Or '@ang3_odoo_api.client' for the default connection
```

For each client, the bundle creates a public alias following this naming convention: 
```ang3_odoo_api.client.<connection_name>```.

Upgrades
========

### From 1.* to 2.*

What you have to do:
- Replace ```Ang3\Component\Odoo\ExternalApiClient``` with ```Ang3\Component\Odoo\Client```.
- Create and use your own helpers.

Logs:
- Updated composer.json for Symfony ```^5.0``` support.
- Updated client to version ```5.0``` for latest feature (expression builder).
- Deleted helpers before the model names depend on Odoo server version.