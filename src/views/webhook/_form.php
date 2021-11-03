<?php

use doohlabs\webhooks\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \doohlabs\webhooks\models\Webhook */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $module = Module::getInstance(); ?>

<div class="webhook-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'model')->dropDownList(array_combine($module->allowedModels, $module->allowedModels)) ?>

    <?= $form->field($model, 'event')->dropDownList(array_combine($module->allowedEvents, $module->allowedEvents)) ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?php $methods = ['POST', 'GET', 'PUT', 'DELETE'] ?>
    <?= $form->field($model, 'method')->dropDownList(array_combine($methods, $methods)) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
