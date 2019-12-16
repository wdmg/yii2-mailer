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
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'email_from',
            'email_to',
            'email_subject',
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
                'attribute' => 'status',
                'format' => 'html',
                'label' => Yii::t('app/modules/mailer', 'Status'),
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
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
            'created_at:datetime',
            'updated_at:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/mailer','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'buttons'=> [
                    'view' => function($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['mailer/view', 'id' => $data->id]), [
                            'class' => 'mailer-details-link',
                            'title' => Yii::t('yii', 'View'),
                            'data-id' => $key,
                            'data-pjax' => '0'
                        ]);
                    },
                    'download' => function($url, $data, $key) use ($module) {
                        $sourcePath = \yii\helpers\BaseFileHelper::normalizePath(Yii::getAlias($module->mailsPath) .'/'. $data->email_source);
                        if ($data->email_source && file_exists($sourcePath))
                            return Html::a('<span class="glyphicon glyphicon-download"></span>', Url::to(['mailer/download', 'source' => $data->email_source]), [
                                'class' => 'mailer-download-link',
                                'title' => Yii::t('yii', 'Download'),
                                'data-id' => $key,
                                'data-pjax' => '0'
                            ]);
                        else
                            return false;
                    },
                ],
                'template' => '{view}&nbsp;{download}'
            ]
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => '',
            'nextPageCssClass' => '',
            'firstPageCssClass' => 'previous',
            'lastPageCssClass' => 'next',
            'firstPageLabel' => Yii::t('app/modules/mailer', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/mailer', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/mailer', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/mailer', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>

    <div class="btn-group">
        <?= Html::a(Yii::t('app/modules/mailer', 'Download report'), ['mailer/export'], [
            'class' => 'btn btn-info',
            'data-pjax' => '0'
        ]) ?>
        <?= Html::a(Yii::t('app/modules/mailer', 'Delete all'), ['mailer/clear'], ['class' => 'btn btn-danger']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
