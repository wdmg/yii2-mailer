<?php

namespace wdmg\mailer\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MailerController implements actions.
 */
class MailerController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'delete' => ['post'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'export' => ['get'],
                    'import' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $viewed = array();
        $session = Yii::$app->session;

        if(isset($session['viewed-flash']) && is_array($session['viewed-flash']))
            $viewed = $session['viewed-flash'];

        if (!(Yii::$app->getMailer()) && !in_array('mailer-need-mailer', $viewed) && is_array($viewed)) {
            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/mailer',
                    'The mailer component must be configured in the application.'
                )
            );
            $session['viewed-flash'] = array_merge(array_unique($viewed), ['mailer-need-mailer']);
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists of all sending emails.
     * @return mixed
     */
    public function actionIndex()
    {

        $data = [];
        $mailsPath = $this->module->mailsPath;
        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $dir = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($mailsPath));
        $emls = \yii\helpers\BaseFileHelper::findFiles($dir, [
            'only' => ['*.eml']
        ]);

        foreach ($emls as $eml) {

            if (strpos($eml, $dir) !== 0)
                throw new \Exception("Something wrong: {$eml}\n");

            $sourcePath = \yii\helpers\BaseFileHelper::normalizePath($eml);
            $rawEml = fopen($sourcePath, 'r');
            $message = $mailParser->parse($rawEml);
            fclose($rawEml);

            $data[] = [
                'message_id' => $message->getHeaderValue('Message-ID'),
                'datetime' => $message->getHeaderValue('Date'),
                'subject' => $message->getHeaderValue('Subject'),
                'email_from' => $message->getHeaderValue('From'),
                'email_to' => $message->getHeaderValue('To'),
                'email_copy' => $message->getHeaderValue('CC'),
                'mime_version' => $message->getHeaderValue('MIME-Version'),
                'html_content' => $message->getHtmlContent(),
                'html_content' => \yii\helpers\HtmlPurifier::process($message->getHtmlContent()),
                'text_content' => $message->getTextContent(),
                'filename' => basename($sourcePath),
                'source' => $sourcePath
            ];

        }

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['id', 'datetime', 'subject', 'email_from', 'email_to'],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Page model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($messageId)
    {
        $data = [];
        $mailsPath = $this->module->mailsPath;
        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $dir = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($mailsPath));
        $emls = \yii\helpers\BaseFileHelper::findFiles($dir, [
            'only' => ['*.eml']
        ]);

        foreach ($emls as $eml) {

            if (strpos($eml, $dir) !== 0)
                throw new \Exception("Something wrong: {$eml}\n");

            $sourcePath = \yii\helpers\BaseFileHelper::normalizePath($eml);
            $rawEml = fopen($sourcePath, 'r');
            $message = $mailParser->parse($rawEml);
            fclose($rawEml);
            if ($messageId == $message->getHeaderValue('Message-ID')) {
                $data = [
                    'message_id' => $message->getHeaderValue('Message-ID'),
                    'datetime' => $message->getHeaderValue('Date'),
                    'subject' => $message->getHeaderValue('Subject'),
                    'email_from' => $message->getHeaderValue('From'),
                    'email_to' => $message->getHeaderValue('To'),
                    'email_copy' => $message->getHeaderValue('CC'),
                    'mime_version' => $message->getHeaderValue('MIME-Version'),
                    'html_content' => $message->getHtmlContent(),
                    'html_content' => \yii\helpers\HtmlPurifier::process($message->getHtmlContent()),
                    'text_content' => $message->getTextContent(),
                    'filename' => basename($sourcePath),
                    'source' => $sourcePath
                ];
            }
        }

        return $this->render('view', [
            'dataProvider' => $data
        ]);
    }


    /**
     * Download action for email source.
     * @return mixed
     */
    public function actionDownload($messageId) {

        $mailsPath = $this->module->mailsPath;
        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $dir = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($mailsPath));
        $emls = \yii\helpers\BaseFileHelper::findFiles($dir, [
            'only' => ['*.eml']
        ]);
        foreach ($emls as $eml) {

            if (strpos($eml, $dir) !== 0)
                throw new \Exception("Something wrong: {$eml}\n");

            $sourcePath = \yii\helpers\BaseFileHelper::normalizePath($eml);
            $fileName = basename($sourcePath);
            $rawEml = fopen($sourcePath, 'r');
            $message = $mailParser->parse($rawEml);

            if ($messageId == $message->getHeaderValue('Message-ID')) {
                return Yii::$app->response->sendFile($eml, $fileName, [
                    'mimeType' => 'multipart/alternative'
                ]);
            }
            fclose($rawEml);
        }
        return $this->goBack(['index']);
    }

}
