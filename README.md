TwLogger
=======

A tool to index profiling data to your elastic search cluster.

This tool requires that [XHProf](http://pecl.php.net/package/xhprof) or its one
of its forks [Uprofiler](https://github.com/FriendsOfPHP/uprofiler),
[Tideways](https://github.com/tideways/php-profiler-extension) are installed.
XHProf/Tideways is a PHP Extension that records and provides profiling data.

System Requirements
===================

TwLogger has the following requirements:

 * PHP version 5.5 or later.
 * [XHProf](http://pecl.php.net/package/xhprof),
   [Uprofiler](https://github.com/FriendsOfPHP/uprofiler) or
   [Tideways](https://github.com/tideways/php-profiler-extension) to actually profile the data.

Installation
============

1. Clone or download `TwLogger` from Github.

2. run composer to install dependencies.

Configuration
=============

Configure TwLogger Profiling Rate
-------------------------------

After installing TwLogger, you may want to do change how frequently you
profile the host application. The `profiler.enable` configuration option
allows you to provide a callback function that specifies the requests that
are profiled. By default, TwLogger profiles 1 in 100 requests.

The following example configures TwLogger to only profile requests
from a specific URL path:

The following example configures TwLogger to profile 1 in 100 requests,
excluding requests with the `/blog` URL path:

```php
// In config/config.php
return array(
    // Other config
    'profiler.enable' => function() {
        $url = $_SERVER['REQUEST_URI'];
        if (strpos($url, '/blog') === 0) {
            return false;
        }
        return rand(1, 100) === 42;
    }
);
```

In contrast, the following example configured TwLogger to profile *every*
request:

```php
// In config/config.php
return array(
    // Other config
    'profiler.enable' => function() {
        return true;
    }
);
```


Configure 'Simple' URLs Creation
--------------------------------

TwLogger generates 'simple' URLs for each profile collected. These URLs are
used to generate the aggregate data used on the URL view. Since
different applications have different requirements for how URLs map to
logical blocks of code, the `profile.simple_url` configuration option
allows you to provide specify the logic used to generate the simple URL.
By default, all numeric values in the query string are removed.

```php
// In config/config.php
return array(
    // Other config
    'profile.simple_url' => function($url) {
        // Your code goes here.
    }
);
```

The URL argument is the `REQUEST_URI` or `argv` value.


Profile an Application or Site
==============================

The simplest way to profile an application is to use
`external/header.php`. `external/header.php` is designed to be combined
with PHP's
[auto_prepend_file](http://www.php.net/manual/en/ini.core.php#ini.auto-pr
epend-file) directive. You can enable `auto_prepend_file` system-wide
through `php.ini`. Alternatively, you can enable `auto_prepend_file` per
virtual host.

With apache this would look like:

```apache
<VirtualHost *:80>
  php_admin_value auto_prepend_file "/path/to/TwLogger/external/header.php"
  ...
</VirtualHost>
```
With Nginx in fastcgi mode you could use:

```nginx
server {
  listen 80;
  ...
  fastcgi_param PHP_VALUE "auto_prepend_file=/path/to/TwLogger/external/header.php";
}
```

Profile a CLI Script
====================

The simplest way to profile a CLI is to use
`external/header.php`. `external/header.php` is designed to be combined with PHP's
[auto_prepend_file](http://www.php.net/manual/en/ini.core.php#ini.auto-prepend-file)
directive. You can enable `auto_prepend_file` system-wide
through `php.ini`. Alternatively,
you can enable include the `header.php` at the top of your script:

```php
<?php
require '/path/to/TwLogger/external/header.php';
// Rest of script.
```

You can alternatively use the `-d` flag when running php:

```bash
php -d auto_prepend_file=/path/to/TwLogger/external/header.php do_work.php
```

Saving & Importing Profiles
---------------------------

Be aware of file locking: depending on your workload, you may need to
change the `save.handler.filename` file path to avoid file locking
during the import.

The following demonstrate the use of `external/import.php`:

```bash
php external/import.php -f /path/to/file
```

**Warning**: Importing the same file twice will index twice, resulting in duplicate profiles

Using Tideways Extension
========================

The XHProf PHP extension is not compatible with PHP7.0+. Instead you'll need to
use the [tideways extension](https://github.com/tideways/php-profiler-extension).

Once installed, you can use the following configuration data:

```ini
[tideways]
extension="/path/to/tideways/tideways.so"
tideways.sample_rate=100
```

Releases / Changelog
====================

See the [releases](https://github.com/nishantsaini/twlogger/releases) for changelogs,
and release information.

Credits
=======

To all developers and contributors of https://github.com/preinheimer/xhgui

License
=======

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

