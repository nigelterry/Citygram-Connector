Yii 2 Basic Project Template
============================

Yii 2 Basic Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-basic/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii2-app-basic/downloads.png)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-basic.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-basic)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.


INSTALLATION
------------

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](http://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
php composer.phar global require "fxp/composer-asset-plugin:~1.0.0"
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTE:** Yii won't create the database for you, this has to be done manually before you can access it.

Also check and edit the other files in the `config/` directory to customize your application.

Adding Datasources
==================

To add a new dataset, you can use existing files...copy/rename them. For
example, lets add the 'Crash Data' for Cary.

https://data.townofcary.org/explore/dataset/cpd-crash-incidents/api/?disjunctive.rdfeature&disjunctive.rdcharacter&disjunctive.rdclass&disjunctive.rdconfigur&disjunctive.rdsurface&disjunctive.rdcondition&disjunctive.lightcond&disjunctive.weather&disjunctive.trafcontrl&disjunctive.month&disjunctive.contributing_factor&disjunctive.vehicle_type&location=10,35.79992,-78.64599

First make a new Crash Report TYPE. Copy `models/PoliceReport.php` to `models/CrashReport.php`.
Change the class name appropriately (CrashReport).

Then we setup the report for the Cary data source. Copy `models/PoliceReportC.php` to `models/CrashReportC.php`
Change class names. Change all instances of ‘police’ to ‘crash’. This shows the report for the Cary data.

Next we need to populate all the methods in the parent `models/Report.php` class.

 * getData(): This pulls the URL where the data comes from. In this case its at the bottom of the code for Cary API tab. Also, you probably need to filter on date. Find the appropriate datetime column to do that. Get the ‘number of hits’ column from the JSON response...the data we get back should be sorted by crash date too.
 * datatime() - crash_date in this case.
 * id() - a unique id. We’ll use tamainid.
 * properties() - are all the fields. Same as the source.
 * geometry() - find the geometry. Again it was the same as the police data.
 * other() - all the other records.
 * title() - what are we going to say to the humans: summarizing the date. Here we went with vehicle1 and vehicle2 as our summary.
 * popupContent() - same idea.

Copy `models/CrimeMessage.php` to `models/CrashMessageSearch.php`.
Change class names. Change ‘police’ to ‘crash’ within the file. This is responsible for the message that is exported to the citygram geojson feed.

Copy `models/CrimeMessageSearch.php` to `models/CrashMessageSearch.php`
Change class names. It would be used for the citygram-connector filtering (but we don’t do much with that now).

Copy `models/CrimeReportCSearch.php` to `models/CrashReportCSearch.php`
Change class names.

Copy `controllers/PoliceReportController.php` to `controllers/CrashReportController.php`
Change class names. Rename names appropriately.

Copy `controllers/CrimeMessageController.php` to `controllers/CrashMessageController.php`
Change class names. Rename names appropriately.

Copy `controllers/PoliceReportCController.php` to `controllers/CrashReportCController.php`
Change class names. Rename names appropriately.

Finally, load the data into the database:

    # last 365 days
    ./yii.sh load CrashReport 365
