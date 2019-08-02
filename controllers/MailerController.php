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
     * Lists of all sending emails.
     * @return mixed
     */
    public function actionIndex()
    {
        $id = 0;
        $data = [];
        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $dir = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias('@runtime/mail'));
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

            $id++;
            $data[] = [
                'id' => $id,
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
     * Download action for email source.
     * @return mixed
     */
    public function actionDownload($messageId) {

        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $dir = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias('@runtime/mail'));
        $emls = \yii\helpers\BaseFileHelper::findFiles($dir, [
            'only' => ['*.eml']
        ]);
        foreach ($emls as $eml) {

            if (strpos($eml, $dir) !== 0)
                throw new \Exception("Something wrong: {$eml}\n");

            $sourcePath = \yii\helpers\BaseFileHelper::normalizePath($eml);
            $fileName = pathinfo($sourcePath, PATHINFO_BASENAME);
            $rawEml = fopen($sourcePath, 'r');
            $message = $mailParser->parse($rawEml);

            if ($messageId == $message->getHeaderValue('Message-ID')) {
                Yii::$app->response->sendStreamAsFile($rawEml, $fileName, [
                    'mimeType' => 'multipart/alternative'
                ])->send();
            }
            fclose($rawEml);
        }
        return $this->redirect(['index']);
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
        $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
        $dir = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias('@runtime/mail'));
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
                    'source' => $sourcePath
                ];
            }
        }

        return $this->render('view', [
            'dataProvider' => $data
        ]);
    }

}
