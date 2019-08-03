<?php

namespace wdmg\mailer;

/**
 * Yii2 Mailer
 *
 * @category        Module
 * @version         1.0.1
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
    private $version = "1.0.1";

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

        // Send mail event
        if (!($app instanceof \yii\console\Application) && $this->module && ($app->mailer instanceof \yii\base\Component)) {
            \yii\base\Event::on(\yii\mail\BaseMailer::className(), \yii\mail\BaseMailer::EVENT_AFTER_SEND, function ($event) {
                $sendStatus = $event->isSuccessful;
                $message = $event->message;
                $mailer = $event->sender;
                if ($this->saveMails && !$mailer->useFileTransport) {
                    $messageFile = $mailer->generateMessageFileName();
                    $messagePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($this->mailsPath) .'/'. $messageFile);
                    file_put_contents($messagePath, $message->toString());
                }
            });
        }
    }
}