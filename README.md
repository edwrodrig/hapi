edwrodrig\hapi
========
Mi nueva biblioteca para manejar HTTP requests y responses.

[![Latest Stable Version](https://poser.pugx.org/edwrodrig/hapi/v/stable)](https://packagist.org/packages/edwrodrig/hapi)
[![Total Downloads](https://poser.pugx.org/edwrodrig/hapi/downloads)](https://packagist.org/packages/edwrodrig/hapi)
[![License](https://poser.pugx.org/edwrodrig/hapi/license)](https://github.com/edwrodrig/hapi/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/edwrodrig/hapi.svg?branch=master)](https://travis-ci.org/edwrodrig/hapi)
[![codecov.io Code Coverage](https://codecov.io/gh/edwrodrig/hapi/branch/master/graph/badge.svg)](https://codecov.io/github/edwrodrig/hapi?branch=master)
[![Code Climate](https://codeclimate.com/github/edwrodrig/hapi/badges/gpa.svg)](https://codeclimate.com/github/edwrodrig/hapi)
![Hecho en Chile](https://img.shields.io/badge/country-Chile-red)

Por el momento lo que puedo decir es que va a reemplazar a mi antigua biblioteca [http_services](https://github.com/edwrodrig/http_services).
Próximamente se desvincularán el código base en una biblioteca core. 

## Instalación
```
composer require edwrodrig/hapi
```

## Información de mi máquina de desarrollo
Salida de [system_info.sh](https://github.com/edwrodrig/hapi/blob/master/scripts/system_info.sh)
```
+ hostnamectl
+ grep -e 'Operating System:' -e Kernel:
  Operating System: Ubuntu 20.04 LTS
            Kernel: Linux 5.4.0-33-generic
+ php --version
PHP 7.4.3 (cli) (built: May 26 2020 12:24:22) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.3, Copyright (c), by Zend Technologies
    with Xdebug v2.9.2, Copyright (c) 2002-2020, by Derick Rethans
```

## Notas
  - El código se apega a las recomendaciones de estilo de [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md).
  - Este proyecto esta pensado para ser trabajado usando [PhpStorm](https://www.jetbrains.com/phpstorm).
  - Se usa [PHPUnit](https://phpunit.de/) para las pruebas unitarias de código.
  - Para la documentación se utiliza el estilo de [phpDocumentor](http://docs.phpdoc.org/references/phpdoc/basic-syntax.html). 

