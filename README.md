CalendArt
=========
<!-- BADGES HERE WHEN IT SHALL BE OPENED ! //-->
Interface to handle all your calendars through an unified API, whatever their
source are (Google, Office 365, ... etc), as if it was an art. Hell yeah.

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
2. Run the following command:
```
$ composer require calendArt/calendArt
```

Running Tests
=============
```console
$ php composer.phar install --dev
$ phpunit
```

Credits
=======
Made with love by [@wisembly](http://wisembly.com/en/)
