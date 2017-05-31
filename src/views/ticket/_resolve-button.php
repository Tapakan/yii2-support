<?php

/* @var $this yii\web\View */
/* @var $model \hexa\yiisupport\models\Priority */

use hexa\yiisupport\helpers\Html;

echo Html::a(
    Yii::t('ticket', 'Resolve'),
    ['resolve', 'id' => $model->id],
    ['class' => 'btn btn-success']
);
