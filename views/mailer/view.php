<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/mailer', 'View mail');
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['mailer/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="mailer-view">

    <?= DetailView::widget([
        'model' => $dataProvider,
        'attributes' => [
            [
                'attribute' => 'message_id',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Message ID'),
            ],
            [
                'attribute' => 'datetime',
                'format' => 'datetime',
                'label' => Yii::t('app/modules/mailer', 'Date/time'),
            ],
            [
                'attribute' => 'subject',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Subject'),
            ],
            [
                'attribute' => 'email_from',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Email from'),
            ],
            [
                'attribute' => 'email_to',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Email to'),
            ],
            [
                'attribute' => 'email_copy',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Email to (copy)'),
            ],
            [
                'attribute' => 'mime_version',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'MIME-version'),
            ],
            [
                'attribute' => 'html_content',
                'format' => 'raw',
                'label' => Yii::t('app/modules/mailer', 'HTML-content'),
                'value' => function($data) {
                    $output = $data['html_content'];

                    // Clearing tracking URL
                    $trackingRoute = $this->context->module->trackingRoute;
                    $output = preg_replace('/(\\'.$trackingRoute.'\/track[\?\&\&amp\;]+url\=)/', '', $output);
                    $output = preg_replace( '/([\?\&\&amp\;]+key\=+[A-Za-z0-9-_]*+|[\?\&\&amp\;]!)/', '', $output);
                    return $output;
                }
            ],
            [
                'attribute' => 'text_content',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Text-сontent'),
            ],
            [
                'attribute' => 'source',
                'format' => 'html',
                'label' => Yii::t('app/modules/mailer', 'Source'),
                'value' => function($data) {
                    return Html::a($data['filename'], Url::to(['mailer/download', 'messageId' => $data['message_id']]), [
                        'target' => '_blank',
                        'data-pjax' => '0'
                    ]);
                }
            ],
        ],
    ]); ?>

</div>