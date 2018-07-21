# JBZoo Community Edition [![Build Status](https://travis-ci.org/JBZoo/JBZoo.svg?branch=master)](https://travis-ci.org/JBZoo/JBZoo)    [![Latest Stable Version](https://poser.pugx.org/jbzoo/jbzoo/v/stable)](https://github.com/JBZoo/JBZoo/releases)    [![License](https://poser.pugx.org/JBZoo/JBZoo/license)](https://packagist.org/packages/JBZoo/JBZoo)

[![version-badge][version-badge]][versions]

FREE Community Edition

 * All Features are included
 * GPL v2.0 or later license
 * No ioncube or any other PHP-encoders (open source)
 * No actiovations
 * No domain limits
 * For any private/commercial use (see license conditions)
 * On your own risk (see license conditions)
 * And of course you can add your fixes via [Github (PR)](https://github.com/JBZoo/JBZoo/blob/master/PULL_REQUEST_TEMPLATE.md)


## System requirements
 * YooTheme Zoo Component [![Build Status](https://img.shields.io/badge/Zoo-3.3.31-blue.svg?longCache=true&style=plastic)](https://www.yootheme.com/zoo)
 * Joomla! CMS [![Joomla](https://img.shields.io/badge/Joomla!-3.8.10-blue.svg?longCache=true&style=plastic)](https://downloads.joomla.org/)
 * PHP `>= 7.2.5` is recommended with modules (mbstring, xml, json, opcache, mysqli)
 * Works fine with PHP v5.5, v5.6, v7.1
 * Joomla! CMS 4.0 (on your own risk)

## How to GPL previous JBZoo version <= 2.4.x ?
First of all, we **strongly recommend** you to upgrade your JBZoo to the latest paid version 2.4.x
After that just use special patch from [that repository](https://github.com/JBZoo/JBZoo-2-GPL-patches)

## How to build Joomla installer/update package (distr)?
Just run in the root directory of project `make prod build` and see `./build/` folder

## I found a bug. What I have to do?
Just create [new issue](https://github.com/JBZoo/JBZoo/issues/new/choose)

## Releases
 * [All Stable Versions](https://github.com/JBZoo/JBZoo/releases) [![Latest Stable Version](https://poser.pugx.org/jbzoo/jbzoo/v/stable)](https://github.com/JBZoo/JBZoo/releases) See file `jbzoo_clean_install.zip` or `jbzoo_update.zip`
 * [Last Unstable](https://github.com/JBZoo/JBZoo/archive/master.zip) [![Latest Unstable Version](https://poser.pugx.org/jbzoo/jbzoo/v/unstable)](https://github.com/JBZoo/JBZoo/archive/master.zip)
 * [Old Paid Versions](http://clientarea.jbzoo.com)

## Useful links
### General websites
 * [English](http://jbzoo.com)
 * [Russian](http://jbzoo.ru)
 * [JBZoo Demo](http://demo.jbzoo.com)
 * [Forum & Community](http://forum.jbzoo.com)
 * [JBZoo Marketplace (extentions)](http://forum.jbzoo.com/files/)

### Our Related Projects
 * [JBZoo CLI extention](https://github.com/JBZoo/CCK-Cli)
 * [JBlank Template](https://github.com/JBZoo/JBlank)
 * [Our libs and tools](https://github.com/JBZoo)

### About YooTheme ZOO
 * [YT Zoo Component (Original)](https://www.yootheme.com/zoo)
 * [YT Zoo Component (JBZoo Community Edition)](https://github.com/JBZoo/YOOtheme-Zoo)
 * [YT Zoo Performance Hacks](https://github.com/JBZoo/Zoo-Hacks)


## Contributors
 * [Denis Smetannikov](https://github.com/SmetDenis) (SmetDenis) `-=! Author, Founder and General Developer !=-`
 * [Sergey Kalistratov](https://github.com/Cheren) (Cheren)
 * [Alexandr Oganov](https://github.com/Tapakan) (Tapakan)
 * [Eugene Kopylov](https://github.com/CB9TOIIIA) (CB9TOIIIA)
 * [And many others...](https://github.com/JBZoo/JBZoo/graphs/contributors)


## PHP Unit tests
Unfortunately now we are checking only copyrights and some code styles. [See details](https://travis-ci.org/JBZoo/JBZoo).

```sh
make dev
make test
```

## LICENSE
GNU GPL v2.0 or later. [See details](https://github.com/JBZoo/JBZoo/blob/master/LICENSE.md)
