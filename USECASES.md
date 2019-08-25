# Usecase


## Tracking
To track viewing email messages you must be set to `true` for `trackMails` parameter, as well as configure the desired route in the parameter` trackingRoute` in the module configuration (by default, this route is setup as `/mail` which brings your links to view like: `/mail/track?url=SOME_URL&key=UNIQUE_KEY`).

Further, in each sent message it is necessary to provide loading an image in the body of the letter using a special tracking key or integrate such a tracking key directly into the main link by which user transition is assumed.
 
A unique tracking key for each email you send is automatically generated and is stored in the database and is accessible from the application parameters `Yii::$app->params["mailer.trackingKey"]`.

An example of using a unique tracking key for image in mail view of your application:

    <?php
        /*
        *   Your email view
        */
        
        use yii\helpers\Html;
        use yii\helpers\Url;
        use yii\web\AssetBundle as AppAsset;
        
        $bundle = AppAsset::register($this);
        if (isset(Yii::$app->params["mailer.trackingKey"]))
            $logotype = Url::to(Url::home(true) . 'mail/track?url=' . $bundle->baseUrl . '/images/logo.png&key=' . Yii::$app->params["mailer.trackingKey"]);
        else
            $logotype = Url::to(Url::home(true) . $bundle->baseUrl . '/images/logo.png');
            
        echo Html::a(Html::img($logotype, ['style' => "width:160px;"]), Url::home(true));
        
    ?>
    
Such code will generate something like this in the body of the sent email message:

    <a href="http://example.com/"><img src="http://example.com/mail/track?url=/assets/e608a8aa/images/logo.png&key=3DivG_3cEAp44HgmghgGqGTeQhQeGKlNxv" style="width:160px;"></a>
    
When the mail client requests an image in the body of the letter, the module compares the tracking key and determines the sent message as read, and also returns the requested image.

If you selected a link for tracking, the user will also be moved to the specified link.

## Web versions
You can specify a link to the web version of the sent message, which will be generated and saved in the web browser automatically at the time of sending.

If the option to save the web version of the mail message is enabled in the module configuration, you can use this code to generate the link in mail view of your application:

    if (isset(Yii::$app->params["mailer.webMailUrl"])) {
        $webMailUrl = Url::to(Yii::$app->params["mailer.webMailUrl"]);
        echo Yii::t('app/modules/admin', 'Do not see the images? Go to the {link} of this email.', [
            'link' => Html::a('web version', $webMailUrl),
        ]);
    }