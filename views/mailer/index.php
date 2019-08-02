<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="mailer-index">

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            /*'message_id',*/
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
                'label' => Yii::t('app/modules/mailer', 'From'),
            ],
            [
                'attribute' => 'email_to',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'To'),
            ],
            [
                'attribute' => 'text_content',
                'format' => 'text',
                'label' => Yii::t('app/modules/mailer', 'Content'),
                'value' => function($data) {
                    if ($data['text_content'])
                        return mb_strimwidth($data['text_content'], 0, 155, '…');
                    elseif ($data['html_content'])
                        return mb_strimwidth(strip_tags($data['html_content']), 0, 155, '…');
                    else
                        return null;
                }
            ],
            [
                'attribute' => 'source',
                'format' => 'html',
                'label' => Yii::t('app/modules/mailer', 'Source'),
                'value' => function($data) {
                    return Html::a($data['filename'], Url::to(['mailer/download', 'messageId' => $data['message_id']]));
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/pages','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'buttons'=> [
                    'view' => function($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['mailer/view', 'messageId' => $data['message_id']]), [
                            'class' => 'mailer-details-link',
                            'title' => Yii::t('yii', 'View'),
                            'data-id' => $key,
                            'data-pjax' => '0'
                        ]);
                    },
                    'download' => function($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-download"></span>', Url::to(['mailer/download', 'messageId' => $data['message_id']]), [
                            'class' => 'mailer-download-link',
                            'title' => Yii::t('yii', 'Download'),
                            'data-id' => $key,
                            'data-pjax' => '0'
                        ]);
                    },
                ],
                'template' => '{view}&nbsp;{download}'
            ]
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
