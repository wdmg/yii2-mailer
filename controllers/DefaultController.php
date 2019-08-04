<?php

namespace wdmg\mailer\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use wdmg\mailer\models\Mails;

/**
 * DefaultController implements public actions for Mails model.
 */
class DefaultController extends Controller
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
                    'track' => ['get'],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * Default action.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(Yii::$app->homeUrl);
    }


    /**
     * Tracking action for email messages.
     * @return mixed
     */
    public function actionTrack($key = null, $url = null) {

        // Find mail by tracking key and updaye viewed flag
        if ($key) {
            if ($model = $this->findModel($key)) {
                $model->is_viewed = true;
                $model->update();
            }
        }

        // Redirect to requested URL or home URL
        if ($url)
            return $this->redirect($url);
        else
            return $this->redirect(Yii::$app->homeUrl);

    }

    /**
     * Finds the Mail model based on its tracking key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return mixing, the loaded model or false
     */
    protected function findModel($key)
    {
        if (($model = Mails::findOne(['tracking_key' => $key])) !== null)
            return $model;
        else
            return false;
    }
}
