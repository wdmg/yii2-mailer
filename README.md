[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-mailer/total.svg)](https://GitHub.com/wdmg/yii2-mailer/releases/)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-mailer.svg)](https://github.com/wdmg/yii2-mailer/blob/master/LICENSE)
![GitHub release](https://img.shields.io/github/release/wdmg/yii2-mailer/all.svg)

# Yii2 Mailer
Mail manager for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.33 and newest
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
* v.1.3.4 - Up to date dependencies
* v.1.3.3 - Added pagination and report export
* v.1.3.2 - Fixed deprecated class declaration
* v.1.3.1 - Some bugs fixed