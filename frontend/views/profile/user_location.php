

<?php

use yii\bootstrap4\ActiveForm;

/** @var \yii\web\View $this */
/** @var \common\models\UserLocation $userLocation */


?>


<?php if(isset($success) && $success): ?>
<div class="alert alert-success">
    Your contact has been successfully updated
</div>
<?php endif ?>


<?php $AddressForm = ActiveForm::begin([
    'action' => ['/profile/update-location'],
    'options' => [
        'data-pjax' => 1
    ]
]); ?>
<?= $AddressForm->field($userLocation, 'address') ?>
<?= $AddressForm->field($userLocation, 'city') ?>
<?= $AddressForm->field($userLocation, 'state') ?>
<?= $AddressForm->field($userLocation, 'county') ?>
<?= $AddressForm->field($userLocation, 'zipcode') ?>
<button class="btn btn-primary">Update</button>
<?php ActiveForm::end() ?>

