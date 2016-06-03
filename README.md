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

### Developer Setup in Vagrant

You can get the app up and running quickly with
[Vagrant](http://vagrantup.com). Do:

    # setup a box with php and composer.
    vagrant up

    # login...
    vagrant ssh

    # go to the hosted directory
    cd /vagrant

    # start up the test server (run on global port for vagrant to pick it up)
    ./yii serve 0.0.0.0

Now you can go to [localhost:8080](http://localhost:8080) to see the site.

You can login with user: admin, and password: admin

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

Basic App Structure and Data Flow
=================================

This connector sits between one or more sources of data, and gets records from the source through an api or by scraping information. These records are considered REPORTS and there will be a data model and mongodb collection associated with that Report. As an example if the data source was Police Records from Cary, The model would best be called PoliceReportCary. The app will create a collection named police_report_cary and will provide a set of end points like:
http://domain/police-report-cary/api
http://domain/police-report-cary/index
http://domain/police-report-cary/view
http://domain/police-report-cary/dump
http://domain/police-report-cary/item
Explore these later.

As the REPORT information is retrieved it is processed and will be added to a data model that is a MESSAGE. For each type of message there will be one MESSAGE. In this case since the messages are about crime, we will call the model CrimeMessage and the mongodb collection will be crime_message. Similar end points to the above are available at http://domain/crime-message. The most important of these is http://domain/crime-message/api which is the endpoint that should be called from the main http://Citygram.org instance. This is also the deafult action, so http://domain/crime-message will suffice. If you want a human readable version try http://domain/crime-message?pretty=true.

Note that to view the index, view and dump pages, you will need to login to the app. Use user = demo, password = demo.

Also note that if the retrieved REPORT data does not contain geographical location information, no MESSAGE will be built as http://Citygram.org doesn't handle messages without a location.

Adding Datasources
==================

To add a new dataset, you can use existing files...copy/rename them. The files you need to generate will go in /models/. 

For example, lets add the 'Crash Data' for Cary. We will create models/CrashReportCary.php & models/CrimeMessage.php. The easiest way to start is to copy the Cary files PoliceReportCary & CrimeReports. They are the best commented of the models.

The full url for the Town of Cary endpoint is 
https://data.townofcary.org/explore/dataset/cpd-crash-incidents/api/?disjunctive.rdfeature&disjunctive.rdcharacter&disjunctive.rdclass&disjunctive.rdconfigur&disjunctive.rdsurface&disjunctive.rdcondition&disjunctive.lightcond&disjunctive.weather&disjunctive.trafcontrl&disjunctive.month&disjunctive.contributing_factor&disjunctive.vehicle_type&location=10,35.79992,-78.64599
however we only need 
https://data.townofcary.org/api/records/1.0/search/?dataset=cpd-crash-incidents. 

The REPORT model has 7 required methods:

 * getData(): This pulls the URL where the data comes from. In this case its at the bottom of the code for Cary API tab. Also, you probably need to filter on date. Find the appropriate datetime column to do that. Get the ‘number of hits’ column from the JSON response...the data we get back should be sorted by crash date too.
 * datatime() - crash_date in this case.
 * id() - a unique id. We’ll use tamainid.
 * properties() - are all the fields. Same as the source.
 * geometry() - find the geometry. Again it was the same as the police data.
 * other() - all the other records.
 * title() - what are we going to say to the humans: summarizing the date. Here we went with vehicle1 and vehicle2 as our summary.
 * popupContent() - same idea.
 
These methods build the Report and Message documents. See the sources for details.

Finally, load the data into the database:

From the root directory run:

    # last 365 days
    ./yii.sh load CrashReportCary 365
    
Note: If you can't get the data you need directly from an api endpoint, you may need to scrape it. Take a look at PermitReportDurham. This model retrieves some information from an api, but needs to scrape further data to get the date.
