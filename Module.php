<?php

namespace wdmg\mailer;

/**
 * Yii2 Mailer
 *
 * @category        Module
 * @version         1.1.1
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-mailer
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use yii\helpers\ArrayHelper;

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
    private $version = "1.1.1";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 7;

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
     * @var string, route to tracking mails
     */
    public $trackingRoute = "/mail";

    /**
     * @var string, storage message filename
     */
    private $messageFileName;

    /**
     * @var string, storage message tracking key
     */
    private $messageTrackKey;

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

        // Normalize route for tracking messages in frontend
        $this->trackingRoute = self::normalizeRoute($this->trackingRoute);
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

        // Prepare the tracking key
        $this->messageTrackKey = $app->security->generateRandomString(32);

        if ($this->trackMails)
            Yii::$app->params["mailer.trackingKey"] = $this->messageTrackKey;

        // Get mailer
        $mailer = $app->getMailer();

        // If mailer used in test mode set the callback to generate and store message filename
        if ($mailer->useFileTransport)
            $mailer->fileTransportCallback = '\wdmg\mailer\Module::generateMessageFileName';

        // Send mail event
        if (!($app instanceof \yii\console\Application) && $this->module && ($app->mailer instanceof \yii\base\Component)) {
            \yii\base\Event::on(\yii\mail\BaseMailer::className(), \yii\mail\BaseMailer::EVENT_AFTER_SEND, function ($event) {

                $message = $event->message;
                $mailer = $message->mailer;

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

                if ($this->saveMails && !$mailer->useFileTransport) {
                    $this->messageFileName = $mailer->generateMessageFileName();
                    $messagePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($this->mailsPath) .'/'. $this->messageFileName);
                    if (file_put_contents($messagePath, $message->toString())) {
                        $mails->email_source = $this->messageFileName;
                        $mails->is_sended = $event->isSuccessful;
                    }
                } else if ($mailer->useFileTransport) {
                    $this->messageFileName = Yii::$app->params["mailer.messageFileName"];
                    $mails->email_source = $this->messageFileName;
                    $mails->is_sended = false;
                }

                if ($this->trackMails)
                    $mails->tracking_key = $this->messageTrackKey;

                // Validate and save model
                if ($mails->validate())
                    $mails->save();

                // Clear params
                Yii::$app->params["mailer.trackingKey"] = null;
                Yii::$app->params["mailer.messageFileName"] = null;

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

    }

    /**
     * {@inheritdoc}
     */
    public function generateMessageFileName()
    {
        $time = microtime(true);
        $messageFileName = 'test-' . date('Ymd-His-', $time) . sprintf('%04d', (int) (($time - (int) $time) * 10000)) . '-' . sprintf('%04d', mt_rand(0, 10000)) . '.eml';
        Yii::$app->params["mailer.messageFileName"] = $messageFileName;
        return $messageFileName;
    }
}