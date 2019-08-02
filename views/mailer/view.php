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
            /*'id',*/
            'message_id:text',
            'datetime:datetime',
            'subject:text',
            'email_from:text',
            'email_to:text',
            'email_copy:text',
            'mime_version:text',
            'html_content:raw',
            'text_content:text',
            'source'
        ],
    ]); ?>

</div>