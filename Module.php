<?php

namespace wdmg\mailer;

/**
 * Yii2 Mailer
 *
 * @category        Module
 * @version         1.3.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-mailer
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Mailer module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\mailer\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "mailer/index";

    /**
     * @var string, the name of module
     */
    public $name = "Mailer";

    /**
     * @var string, the description of module
     */
    public $description = "Mail manager";

    /**
     * @var string the module version
     */
    private $version = "1.3.0";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 2;

    /**
     * @var boolean, flag if need save mail after send
     */
    public $saveMails = true;

    /**
     * @var string, path to save mails
     */
    public $mailsPath = "@runtime/mail";

    /**
     * @var boolean, flag if need tracking mail after send
     */
    public $trackMails = true;

    /**
     * @var boolean, flag if need save web version of mail`s
     */
    public $saveWebMails = true;

    /**
     * @var string, route to tracking mails
     */
    public $trackingRoute = "/mail";

    /**
     * @var string, route to web mails
     */
    public $webRoute = "/mails";

    /**
     * @var string, path to save web version of sending mail
     */
    public $webMailsPath = "@webroot/mails";

    /**
     * @var boolean, flag for use transport configuration
     */
    public $useTransport = false;

    /**
     * @var array, default transport configuration
     */
    public $transport = [
        'class' => 'Swift_SmtpTransport',
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'port' => '25'
    ];

    /**
     * @var boolean, flag for use encryption in transport
     */
    public $useEncryption = false;

    /**
     * @var array, default encryption configuration
     */
    public $encryption = 'ssl';

    /**
     * @var boolean, flag for use stream options in transport
     */
    public $useStreamOptions = false;

    /**
     * @var array, default stream options
     */
    public $streamOptions =  [
        'ssl' => [
            'allow_self_signed' => false,
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];

    /**
     * @var string, views of mail`s messages
     */
    public $viewPath = '@app/mail';

    /**
     * @var boolean, flag for debug
     */
    public $enableLog = true;

    /**
     * @var integer, message sending interval in sec.
     */
    public $sendingInterval = 1;

    /**
     * @var string, storage message filename
     */
    private $_messageFileName;

    /**
     * @var string, storage message tracking key
     */
    private $_messageTrackKey;

    /**
     * @var string, storage filename to web version of sending mail
     */
    private $_webMailFilename;

    /**
     * @var string, storage URL to web version of sending mail
     */
    private $_webMailUrl;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

        if (!isset($this->mailsPath))
            $this->mailsPath = Yii::$app->getMailer()->fileTransportPath;

        if (!isset($this->webMailsPath) && isset($this->webRoute))
            $this->webMailsPath = "@webroot/" . $this->webRoute;

        // Normalize route for tracking messages in frontend
        $this->trackingRoute = self::normalizeRoute($this->trackingRoute);

        // Normalize route for web version of messages in frontend
        $this->webRoute = self::normalizeRoute($this->webRoute);
    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa-envelope-o',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
        ];
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {

        parent::bootstrap($app);

        if (isset(Yii::$app->params["mailer.saveMails"]))
            $this->saveMails = Yii::$app->params["mailer.saveMails"];

        if (isset(Yii::$app->params["mailer.mailsPath"]))
            $this->mailsPath = Yii::$app->params["mailer.mailsPath"];

        if (isset(Yii::$app->params["mailer.trackMails"]))
            $this->trackMails = Yii::$app->params["mailer.trackMails"];

        if (isset(Yii::$app->params["mailer.saveWebMails"]))
            $this->saveWebMails = Yii::$app->params["mailer.saveWebMails"];

        if (isset(Yii::$app->params["mailer.trackingRoute"]))
            $this->trackingRoute = Yii::$app->params["mailer.trackingRoute"];

        if (isset(Yii::$app->params["mailer.webRoute"]))
            $this->webRoute = Yii::$app->params["mailer.webRoute"];

        if (isset(Yii::$app->params["mailer.webMailsPath"]))
            $this->webMailsPath = Yii::$app->params["mailer.webMailsPath"];

        if (isset(Yii::$app->params["mailer.useTransport"]))
            $this->useTransport = Yii::$app->params["mailer.useTransport"];

        if (isset(Yii::$app->params["mailer.transport"]))
            $this->transport = Yii::$app->params["mailer.transport"];

        if (isset(Yii::$app->params["mailer.useEncryption"]))
            $this->useEncryption = Yii::$app->params["mailer.useEncryption"];

        if (isset(Yii::$app->params["mailer.encryption"]))
            $this->encryption = Yii::$app->params["mailer.encryption"];

        if (isset(Yii::$app->params["mailer.useStreamOptions"]))
            $this->useStreamOptions = Yii::$app->params["mailer.useStreamOptions"];

        if (isset(Yii::$app->params["mailer.streamOptions"]))
            $this->streamOptions = Yii::$app->params["mailer.streamOptions"];

        if (isset(Yii::$app->params["mailer.viewPath"]))
            $this->viewPath = Yii::$app->params["mailer.viewPath"];

        if (isset(Yii::$app->params["mailer.enableLog"]))
            $this->enableLog = Yii::$app->params["mailer.enableLog"];

        if (isset(Yii::$app->params["mailer.sendingInterval"]))
            $this->sendingInterval = Yii::$app->params["mailer.sendingInterval"];

        // Configure the mailer transport
        if ($mailer = Yii::$app->getMailer()) {

            // Configure transport
            if ($this->useTransport) {

                // Apply encryption options for transport
                if ($this->useEncryption && !is_null($this->encryption)) {
                    $this->transport['encryption'] = $this->encryption;
                }

                // Apply stream options for transport
                if ($this->useStreamOptions && !is_null($this->streamOptions)) {
                    if ($this->useStreamOptions && !is_array($this->streamOptions) && !is_object($this->streamOptions)) {
                        throw new InvalidConfigException('"' . get_class($this) . '::streamOptions" should be either object or array, "' . gettype($this->streamOptions) . '" given.');
                    } else {
                        $this->transport['streamOptions'] = $this->streamOptions;
                    }
                }

                // Apply transport configuration
                if (!is_array($this->transport) && !is_object($this->transport)) {
                    throw new InvalidConfigException('"' . get_class($this) . '::transport" should be either object or array, "' . gettype($this->transport) . '" given.');
                } else {
                    $mailer->setTransport($this->transport);
                }

                $mailer->useFileTransport = false;
            } else {
                $mailer->useFileTransport = true;
            }

            // Enable mailer log`s
            if ($this->enableLog)
                $mailer->enableSwiftMailerLogging = $this->enableLog;

            // Set of mailer view`s
            if (!is_null($this->viewPath))
                $mailer->setViewPath($this->viewPath);

        }

        // Get mailer
        $mailer = $app->getMailer();

        // If mailer used in test mode set the callback to generate and store message filename
        if ($mailer->useFileTransport === true)
            $mailer->fileTransportCallback = '\wdmg\mailer\Module::generateMessageFileName';


        // Mail event`s
        if (!($app instanceof \yii\console\Application) && $this->module && ($app->mailer instanceof \yii\base\Component)) {

            \yii\base\Event::on(\yii\mail\BaseMailer::className(), \yii\mail\BaseMailer::EVENT_BEFORE_SEND, function ($event) use ($app) {

                // Set message sending interval
                if (!is_null($this->sendingInterval))
                    sleep(intval($this->sendingInterval));

                // Output message html for Mailer log
                $html = null;

                // Get instance of message
                $message = $event->message;

                // Prepare raw html content
                if ($message instanceof \yii\swiftmailer\Message) {
                    $swiftMessage = $message->getSwiftMessage();
                    $reflection = new \ReflectionObject($swiftMessage);
                    $parent = $reflection->getParentClass()->getParentClass()->getParentClass();
                    $body = $parent->getProperty('_immediateChildren');
                    $body->setAccessible(true);
                    $childs = $body->getValue($swiftMessage);
                    foreach ($childs as $child) {
                        if ($child instanceof \Swift_MimePart && $child->getContentType() == 'text/html') {
                            $html = $child->getBody();
                            break;
                        } else {
                            $html = $message->toString();
                        }
                    }
                } else {
                    $html = $message->toString();
                }

                if ($this->saveWebMails && !is_null($html) && ($filename = $this->getWebMailFilename())) {
                    $rawMessagePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($this->webMailsPath) . '/'. $filename);
                    if (!file_put_contents($rawMessagePath, $html)) {
                        $this->_webMailUrl = null;
                        $html = '';
                    }
                }

            });

            \yii\base\Event::on(\yii\mail\BaseMailer::className(), \yii\mail\BaseMailer::EVENT_AFTER_SEND, function ($event) {

                // Get instance of message
                $message = $event->message;

                // Get instance of mailer
                $mailer = $message->mailer;

                // Get instance of mails model
                $mails = new \wdmg\mailer\models\Mails();

                if (is_array($message->getFrom()))
                    $mails->email_from = implode(", ", array_keys($message->getFrom()));
                else
                    $mails->email_from = $message->getFrom();

                if (is_array($message->getTo()))
                    $mails->email_to = implode(", ", array_keys($message->getTo()));
                else
                    $mails->email_to = $message->getTo();

                if (is_array($message->getCc()))
                    $mails->email_copy = implode(", ", array_keys($message->getCc()));
                else
                    $mails->email_copy = $message->getCc();

                $mails->email_subject = $message->getSubject();

                if ($this->useTransport)
                    $mails->is_sended = $event->isSuccessful;
                else
                    $mails->is_sended = false;

                if ($this->saveMails && $mailer->useFileTransport === false) {
                    $this->_messageFileName = $mailer->generateMessageFileName();
                    $messagePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($this->mailsPath) .'/'. $this->_messageFileName);

                    if (file_put_contents($messagePath, $message->toString()))
                        $mails->email_source = $this->_messageFileName;

                } else if ($mailer->useFileTransport === true) {
                    $this->_messageFileName = Yii::$app->params["mailer.messageFileName"];
                    $mails->email_source = $this->_messageFileName;
                }

                if ($this->trackMails && $trackingKey = $this->getTrackingKey())
                    $mails->tracking_key = $trackingKey;

                if ($this->saveWebMails && $_webMailUrl = $this->getWebMailUrl())
                    $mails->web_mail_url = $_webMailUrl;

                // Validate and save model
                if ($mails->validate())
                    $mails->save();

                // Clear params
                $this->clearSendSession();

            });
        }


        // Add routes to tracking message in frontend
        if ($this->trackMails) {
            $trackingRoute = $this->trackingRoute;
            $app->getUrlManager()->addRules([
                [
                    'pattern' => $trackingRoute . '/<track:[\w-]+>',
                    'route' => 'admin/mailer/default/track',
                    'suffix' => ''
                ],
                $trackingRoute . '/<track:[\w-]+>' => 'admin/mailer/default/track',
            ], true);
        }


        // Configure mailer component
        $app->setComponents([
            'mails' => [
                'class' => 'wdmg\mailer\components\Mails'
            ]
        ]);

    }


    /**
     * Generates and returns a tracking key for a sent message
     *
     * @return null|string, string of tracking key or null if tracking is disabled
     */
    public function genTrackingKey()
    {
        if ($this->trackMails) {
            $trackingKey = Yii::$app->security->generateRandomString(32);
            $this->setTrackingKey($trackingKey);
            return $trackingKey;
        } else {
            return null;
        }
    }

    /**
     * Set the tracking key for a sent message
     *
     * @param $trackingKey, string of tracking key
     */
    public function setTrackingKey($trackingKey)
    {
        if (!is_null($trackingKey))
            $this->_messageTrackKey = $trackingKey;
        else
            $this->_messageTrackKey = null;

    }

    /**
     * Returns the tracking key for a sent message
     *
     * @return null|string, string of tracking key
     */
    public function getTrackingKey()
    {
        if (!is_null($this->_messageTrackKey))
            return $this->_messageTrackKey;
        else
            return null;
    }

    /**
     * Generates and returns URL to the web version of the sent email
     *
     * @return null|string, URL to the web version or null if web version is disabled
     */
    public function genWebMailUrl()
    {
        if ($this->saveWebMails) {
            $filename = \date('Y-m-d-h-i-s') . '_' . \time() . '-' . rand(1000, 9999) . '.html';
            $this->setWebMailFilename($filename);
            $_webMailUrl = \yii\helpers\Url::to(\yii\helpers\Url::home(true) . $this->webRoute . '/' . $filename);
            $_webMailUrl = ltrim(preg_replace('#/{2,}#', '/', $_webMailUrl), '/');
            $this->setWebMailUrl($_webMailUrl);
            return $_webMailUrl;
        } else {
            return null;
        }
    }

    /**
     * Set the filename to the web version of the email
     *
     * @param $_webMailFilename, string of web version filename
     */
    public function setWebMailFilename($_webMailFilename)
    {
        if (!is_null($_webMailFilename)) {
            $this->_webMailFilename = $_webMailFilename;
        } else {
            $this->_webMailFilename = null;
        }
    }

    /**
     * Set the URL to the web version of the email
     *
     * @param $_webMailUrl, string of web version URL
     */
    public function setWebMailUrl($_webMailUrl)
    {
        if (!is_null($_webMailUrl))
            $this->_webMailUrl = $_webMailUrl;
        else
            $this->_webMailUrl = null;

    }

    /**
     * Returns the URL to the web version of the email
     *
     * @return null|string, URL to the web version
     */
    public function getWebMailUrl()
    {
        if (!is_null($this->_webMailUrl))
            return $this->_webMailUrl;
        else
            return null;
    }

    /**
     * Returns the filename of the web version
     */
    public function getWebMailFilename()
    {
        if (!is_null($this->_webMailFilename))
            return $this->_webMailFilename;
        else
            return null;
    }

    /**
     * Clears the system parameters of the send session
     */
    private function clearSendSession()
    {
        Yii::$app->params["mailer.messageFileName"] = null;
        $this->_messageTrackKey = null;
        $this->_messageFileName = null;
        $this->_webMailFilename = null;
        $this->_webMailUrl = null;
    }


    /**
     * {@inheritdoc}
     */
    public static function generateMessageFileName()
    {
        $time = microtime(true);
        $_messageFileName = date('Ymd-His-', $time) . sprintf('%04d', (int) (($time - (int) $time) * 10000)) . '-' . sprintf('%04d', mt_rand(0, 10000)) . '.eml';
        Yii::$app->params["mailer.messageFileName"] = $_messageFileName;
        return $_messageFileName;
    }


    /**
     * {@inheritdoc}
     */
    public function install()
    {
        parent::install();
        $path = Yii::getAlias($this->webMailsPath);

        if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true))
            return true;
        else
            return false;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        parent::uninstall();
        $path = Yii::getAlias($this->webMailsPath);

        if (\yii\helpers\FileHelper::removeDirectory($path))
            return true;
        else
            return false;
    }
}