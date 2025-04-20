# JBZoo CCK - Community Edition

[![Build Status](https://travis-ci.org/JBZoo-CCK/JBZoo.svg)](https://travis-ci.org/JBZoo-CCK/JBZoo)    [![Latest Stable Version](https://poser.pugx.org/jbzoo-cck/jbzoo/v/stable)](https://github.com/JBZoo-CCK/JBZoo/releases)    [![HitCount](http://hits.dwyl.com/jbzoo-cck/jbzoo.svg)](http://hits.dwyl.com/jbzoo-cck/jbzoo)

 * All Features are included
 * GPL v2.0 or later license
 * No ioncube or any other PHP-encoders (open source)
 * No activations
 * No domain limits
 * For any private/commercial use (see license conditions)
 * On your own risk (see license conditions)
 * Of course, you can add your fixes via [Github (PR)](https://github.com/JBZoo-CCK/JBZoo-CCK/blob/master/PULL_REQUEST_TEMPLATE.md)


## System requirements
 * YooTheme Zoo Component [![Zoo](https://img.shields.io/badge/Zoo-4.0.8-blue.svg?style=plastic)](https://www.yootheme.com/zoo)
 * Joomla! CMS [![Joomla](https://img.shields.io/badge/Joomla!-3.9.25-blue.svg?style=plastic)](https://downloads.joomla.org/)
 * ![PHP 7.2.0+](https://img.shields.io/badge/PHP-7.4.0+-blue.svg?style=plastic) is recommended with modules mbstring, xml, json, opcache, mysqli.
 * Works fine with PHP v5.5, v5.6, v7.1, v7.2, v7.3, v7.4, v8.0, v8.1, v8.2, v8.3, v8.4
 * Joomla! CMS 3.9+ / 4.4+ / 5.3+ (on your own risk)

## FAQ
### 1. How to subscribe to updates ?
Watch and star this repo (see buttons in the top) and Github will send you notification. It's easy!

### 2. How to GPL previous JBZoo version <= 2.4.x ?
First of all, we recommend you to upgrade your JBZoo to the latest paid version 2.4.x (only if you wish).
After that, just use a special patch from [that repository](https://github.com/JBZoo-CCK/JBZoo-2-GPL-patches)

### 3. How to build Joomla installer/update package (distr)?
Run in the root directory of project `make prod build` and see `./build/` folder

### 4. I have found some bugs. What should I do?
Just create [new issue](https://github.com/JBZoo-CCK/JBZoo/issues/new/choose) and we will try to fix it.

### 5. How to use jbzoo_update.zip?
 - Backup your website (database, all files)
 - Install file "jbzoo_update.zip" as Joomla Extention (via control Panel).
 - That's all.
 - If you have any bugs - [just create new issue](https://github.com/JBZoo-CCK/JBZoo/issues/new/choose)

### 6. Where I can download the ready-to-use JBZoo?
See files `jbzoo_clean_install.zip` and `jbzoo_update.zip` in [releases](https://github.com/JBZoo-CCK/JBZoo/releases)


## Useful links
### General websites
 * [English](http://jbzoo.com)
 * [Russian](http://jbzoo.ru) and [about GPL version](http://jbzoo.ru/blog/jbzoo-4-gpl)
 * [JBZoo Demo](http://demo.jbzoo.com)
 * [Forum & Community](http://forum.jbzoo.com)
 * [JBZoo Marketplace (extentions)](http://forum.jbzoo.com/files/)

### Our Related Projects
 * [JBZoo CLI extension](https://github.com/JBZoo-CCK/CCK-Cli)
 * [JBlank Template](https://github.com/JBZoo-CCK/JBlank)
 * [Our libs and tools](https://github.com/JBZoo)

### About YooTheme ZOO
 * [YT Zoo Component (Original)](https://www.yootheme.com/zoo)
 * [YT Zoo Component (JBZoo Community Edition)](https://github.com/JBZoo-CCK/YOOtheme-Zoo)
 * [YT Zoo Performance Hacks](https://github.com/JBZoo-CCK/Zoo-Hacks)


## Contributors
 * [Denis Smetannikov](https://github.com/SmetDenis) (SmetDenis) `-=! Author, Founder and General Maintainer !=-`
 * [Dmitriy Vasyukov](https://github.com/fiction13) (fiction13)
 * [Eugene Kopylov](https://github.com/CB9TOIIIA) (CB9TOIIIA)
 * [Sergey Kalistratov](https://github.com/Cheren) (Cheren)
 * [Alexandr Oganov](https://github.com/Tapakan) (Tapakan)
 * ... and [other](https://github.com/JBZoo-CCK/JBZoo/graphs/contributors)


## PHP Unit tests
Unfortunately now we are checking only copyrights, and some code styles. [See details](https://travis-ci.org/JBZoo-CCK/JBZoo).

```sh
make dev
make test
```

## LICENSE
GNU GPL v2.0 or later. [See details](https://github.com/JBZoo/JBZoo/blob/master/LICENSE.md)

