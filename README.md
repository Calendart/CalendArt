CalendArt
=========
<!-- BADGES HERE WHEN IT SHALL BE OPENED ! //-->
Interface to handle all your calendars through an unified API, whatever their
source are (Google, Office 365, ... etc), as if it was an art. Hell yeah.

**Only google calendar is currently supported !**

Installation
============
You have multiple ways to install CalendArt. If you are unsure what to do, go with
[the archive release](#archive-release).

### Archive Release
1. Download the most recent release from the [release page](https://github.com/Wisembly/CalendArt/releases)
2. Unpack the archive
3. Move the files somewhere in your project

### Development version
1. Install Git
2. `git clone git://github.com/Wisembly/CalendArt.git`

### Via Composer
1. Install composer in your project: `curl -s http://getcomposer.org/installer | php`
2. Create a `composer.json` file (or update it) in your project root:

    ```javascript

      {
        "require": {
          "wisembly/calendArt": "~1.0"
        }
      }
    ```

3. Install via composer : `php composer.phar install`

Basic Usage
===========
As there is only Google's adapter, we're going to base these examples on this
one. But it should be similar to the others one, as long as they respect the
interface provided in this package.

```php
<?php

use CalendArt\Util\OAuth2Token,
    CalendArt\Adapter\Google\GoogleAdapter;

$oauth = new OAuth2Token('your-oauth2-token', 'Bearer', 3600); // You can get a OAuth2 token on google's oauth playground
$adapter = new GoogleAdapter($oauth);

$primary = $adapter->getCalendarApi()->get('primary'); // there is always a "primary" calendar on Google
$event   = $adapter->getEventApi($primary)->getList();

var_dump($primary); // Should dump a Calendar instance, with its hydrated events
```

Running Tests
=============
```console
$ php composer.phar install --dev
$ bin/phpunit
```

