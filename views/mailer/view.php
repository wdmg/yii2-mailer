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
                'attribute' => 'email_subject',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Subject'),
            ],
            [
                'attribute' => 'is_sended',
                'format' => 'html',
                'label' => Yii::t('app/modules/mailer', 'Status'),
                'value' => function($data) {
                    if ($data->is_sended)
                        $output = '<span class="label label-success">' . Yii::t('app/modules/mailer', 'Sended') . '</span>';
                    else
                        $output = '<span class="label label-danger">' . Yii::t('app/modules/mailer', 'Not sended') . '</span>';

                    if ($data->is_viewed)
                        $output .= ' <span class="label label-info">' . Yii::t('app/modules/mailer', 'Viewed') . '</span>';
                    else
                        $output .= ' <span class="label label-default">' . Yii::t('app/modules/mailer', 'Not viewed') . '</span>';

                    return $output;
                }
            ],
            [
                'attribute' => 'email_source',
                'format' => 'html',
                'value' => function($data) use ($module) {
                    $sourcePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($module->mailsPath) .'/'. $data->email_source);
                    if ($data->email_source && file_exists($sourcePath))
                        return Html::a($data->email_source, Url::to(['mailer/download', 'source' => $data->email_source]));
                    else
                        return $data->email_source;
                }
            ],
            [
                'attribute' => 'tracking_key',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Tracking key'),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
    <?php if ($message) { ?>
        <h3><?= Yii::t('app/modules/mailer', 'Source data') ?></h3>
        <?= DetailView::widget([
            'model' => $message,
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
                    'value' => function ($data) {
                        $output = $data['html_content'];

                        // Clearing tracking URL
                        $trackingRoute = $this->context->module->trackingRoute;
                        $output = preg_replace('/(\\' . $trackingRoute . '\/track[\?\&\&amp\;]+url\=)/', '', $output);
                        $output = preg_replace('/([\?\&\&amp\;]+key\=+[A-Za-z0-9-_]*+|[\?\&\&amp\;]!)/', '', $output);
                        return $output;
                    }
                ],
                [
                    'attribute' => 'text_content',
                    'format' => 'text',
                    'label' => Yii::t('app/modules/mailer', 'Text-сontent'),
                ],
            ]
        ]);
    } ?>
</div>