<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<!--form to imput the product-->
<div class="product-form">

    <?php $form = ActiveForm::begin([
            'options' => ['enctype'=> 'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(CKEditor::class, [
            'options'=> ['rows' => 6],
            'preset' => 'basic'
    ]) ?>

        <?= $form->field($model, 'imageFile', [
            'template' => ' 
            <div class="custom-file">
                {input}
                {label}
                {error}
            </div>
            ',
        'labelOptions' => ['class' => 'custom-file-label'],
        'inputOptions' => ['class' => 'custom-file-input']

    ])->textInput(['type'=>'file']) ?>

<!--    step attribute allows to specify the values with integer with two digits after the decimal-->
    <?= $form->field($model, 'price')->textInput([
            'maxlength'=>true,
            'type'=>'number',
            'step'=> '0.01'
    ]) ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
