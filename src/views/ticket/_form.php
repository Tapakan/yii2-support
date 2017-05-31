<?php

use hexa\yiisupport\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this       yii\web\View
 * @var $model      \hexa\yiisupport\models\Ticket
 * @var $form       yii\widgets\ActiveForm
 * @var $categories array
 * @var $priorities array
 **/
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'priority_id')->dropdownList($priorities, [
        'prompt' => Yii::t('ticket', 'Select Priority')
    ]); ?>

    <?php echo $form->field($model, 'category_id')->dropdownList($categories, [
        'prompt' => Yii::t('ticket', 'Select Category')
    ]); ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('main', 'Save'), [
            'class' => 'btn btn-primary',
        ]); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
