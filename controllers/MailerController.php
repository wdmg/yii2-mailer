<?php

namespace wdmg\mailer\controllers;

use wdmg\mailer\Module;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use wdmg\mailer\models\Mails;
use wdmg\mailer\models\MailsSearch;

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
                    'track' => ['get'],
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
                'except' => ['track']
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
                ],
                'except' => ['track']
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
    /*public function actionIndex()
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
    }*/
    public function actionIndex()
    {
        $searchModel = new MailsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module
        ]);
    }

    /**
     * Displays a single Page model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $data = null;
        $model = $this->findModel($id);
        $sourcePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($this->module->mailsPath) .'/'. $model->email_source);
        if ($model->email_source && file_exists($sourcePath)) {
            $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
            $rawEml = fopen($sourcePath, 'r');
            $message = $mailParser->parse($rawEml);
            fclose($rawEml);

            $data = [
                'message_id' => $message->getHeaderValue('Message-ID'),
                'datetime' => $message->getHeaderValue('Date'),
                'subject' => $message->getHeaderValue('Subject'),
                'email_from' => $message->getHeaderValue('From'),
                'email_to' => $message->getHeaderValue('To'),
                'email_copy' => $message->getHeaderValue('CC'),
                'mime_version' => $message->getHeaderValue('MIME-Version'),
                'text_content' => $message->getTextContent(),
                'html_content' => \yii\helpers\HtmlPurifier::process($message->getHtmlContent()),
            ];
        }

        return $this->render('view', [
            'module' => $this->module,
            'dataProvider' => $model,
            'message' => $data,
        ]);
    }

    /**
     * Download action for email source.
     * @return mixed
     */
    public function actionDownload($source) {

        $sourcePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($this->module->mailsPath) .'/'. $source);
        if (file_exists($sourcePath))
            return Yii::$app->response->sendFile($sourcePath, $source, [
                'mimeType' => 'multipart/alternative'
            ]);

        return $this->goBack(['index']);
    }

    /**
     * Finds the Mails model by primary key.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Mails model
     * @throws NotFoundHttpException if the model not exist or not published
     */
    protected function findModel($id)
    {
        $model = Mails::findOne($id);

        if (!is_null($model))
            return $model;
        else
            throw new NotFoundHttpException();

    }
}
