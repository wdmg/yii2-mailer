[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.35-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-mailer.svg)](https://packagist.org/packages/wdmg/yii2-mailer)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-mailer.svg)](https://packagist.org/packages/wdmg/yii2-mailer)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-mailer.svg)](https://github.com/wdmg/yii2-mailer/blob/master/LICENSE)

<img src="./docs/images/yii2-mailer.png" width="100%" alt="Yii2 Mailer" />

# Yii2 Mailer
Mail manager and viewer for Yii2.

This module is an integral part of the [Butterfly.СMS](https://butterflycms.com/) content management system, but can also be used as an standalone extension.

Copyrights (c) 2019-2023 [W.D.M.Group, Ukraine](https://wdmg.com.ua/)

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.35 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [PHP MailMimeParser](https://github.com/zbateson/mail-mime-parser) module (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-mailer"`

After configure db connection, run the following command in the console:

`$ php yii mailer/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Clear mails cache

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-mailer/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'mailer' => [
            'class' => 'wdmg\mailer\Module',
            'routePrefix' => 'admin'
            'saveMails' => true, // if need save mail after send
            'mailsPath' => '@runtime/mail', // path to save mails
            'trackMails' => true, // if need tracking mail after send
            'trackingRoute' => '/mail', // route to tracking mails
            'saveWebMails' => false, // flag if need save web version of mail`s
            'webRoute' => '/mails', // route to web mails
            'webMailsPath' => '@webroot/mails', // path to save web version of sending mail
            'sendingInterval' => 1, // message sending interval in sec.
            'useTransport' => false, // flag for use transport configuration
            'transport' => [ // default transport configuration
                'class' => 'Swift_SmtpTransport',
                'host' => 'localhost',
                'username' => '',
                'password' => '',
                'port' => '25'
            ],
            'useEncryption' => false, // flag for use encryption in transport
            'encryption' => 'ssl', // default encryption configuration
            'useStreamOptions' => false, // flag for use stream options in transport
            'streamOptions' => [ // default stream options
                'ssl' => [
                    'allow_self_signed' => false,
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ], 
            'viewPath' => '@app/mail', // views of mail`s messages
            'enableLog' => true // flag for debug
        ],
        ...
    ],


# Usage
See the [USECASES.md](https://github.com/wdmg/yii2-mailer/blob/master/USECASES.md) for more details.

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('mailer')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [ready to use]
* v.1.4.0 - Update copyrights, fix nav menu
* v.1.3.6 - Update README.md and dependencies, clear mails cache from console
* v.1.3.5 - Update README.md and dependencies