[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.20-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-mailer/total.svg)](https://GitHub.com/wdmg/yii2-mailer/releases/)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-mailer.svg)](https://github.com/wdmg/yii2-mailer/blob/master/LICENSE)
![GitHub release](https://img.shields.io/github/release/wdmg/yii2-mailer/all.svg)

# Yii2 Mailer
Mail manager for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.20 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [PHP MailMimeParser](https://github.com/zbateson/mail-mime-parser) module (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-mailer"`

After configure db connection, run the following command in the console:

`$ php yii mailer/init`

# Migrations
Module not have any db migrations.

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'mailer' => [
            'class' => 'wdmg\mailer\Module',
            'routePrefix' => 'admin'
        ],
        ...
    ],


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

# Status and version [in progress development]
* v.1.0.1 - Fixing downloads source of *.eml. Updated translations.
* v.1.0.0 - Added MailMimeParser library
* v.0.0.1 - Added base module, migrations, controllers and translations