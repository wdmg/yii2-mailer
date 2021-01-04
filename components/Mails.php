<?php

namespace wdmg\mailer\components;


/**
 * Yii2 MailerHelper
 *
 * @category        Component
 * @version         1.3.6
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-mailer
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\helpers\Url;

class Mails extends Component
{

    protected $module;
    protected $model;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->module = Yii::$app->getModule('mailer');
        if (is_null($this->module))
            $this->module = Yii::$app->getModule('admin/mailer');

        parent::init();
    }

    /**
     * Generates and returns a email tracking link. Which also includes the original
     * URL passed as an argument.
     *
     * @param $baseUrl, string of original URL to be passed
     * @return string, URL with tracking code and redirect to the original URL or
     * only original URL if email tracking is disabled by config
     */
    public function getTrackingUrl($baseUrl)
    {
        if ($trackingKey = $this->module->genTrackingKey())
            return Url::home(true) . 'mail/track?url=' . $baseUrl . '&key=' . $trackingKey;
        else
            return Url::home(true) . $baseUrl;

    }

    /**
     * Generates and returns a link to the web version of the email.
     * @return null|string, URL to the web version or null if web version is disabled by config
     */
    public function getWebversionUrl() {
        if ($webMailUrl = $this->module->genWebMailUrl()) {
            if (Url::isRelative($webMailUrl))
                return Url::home(true) . $webMailUrl;
            else
                return $webMailUrl;
        } else {
            return null;
        }
    }
}