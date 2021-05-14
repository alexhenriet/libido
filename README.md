Libido
======

General purpose PHP library that I do, hence the name.

Install
-------

Via composer


``` json
{
    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/alexhenriet/libido.git"
      }
    ],
    "require": {
      "alexhenriet/libido": "dev-master"
    }
}
```

``` bash
$ php composer.phar install
```

Use
-------

``` php
<?php

require './vendor/autoload.php';

use Libido\FsRegistry\Registry;
```
