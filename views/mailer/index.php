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

            'id',
            /*'message_id',*/
            'datetime:datetime',
            'subject',
            'email_from',
            'email_to',
            /*'email_copy',*/
            /*'mime_version',*/
            'html_content',
            /*'text_content',*/
            /*'source',*/

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
