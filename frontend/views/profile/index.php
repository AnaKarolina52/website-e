<?php


/** @var \common\models\User $user */
/** @var \yii\web\View $this */
/** @var \common\models\UserLocation $userLocation */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                Contact Information
            </div>
            <div class="card-body">
                <?php \yii\widgets\Pjax::begin([
                    'enablePushState'=>false
                ]) ?>
                <?php echo $this->render('user_location', [
                    'userLocation' => $userLocation
                ]) ?>
                <?php \yii\widgets\Pjax::end() ?>
        </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                My Account
            </div>
            <div class="card-body">
                <?php \yii\widgets\Pjax::begin([
                    'enablePushState'=>false
                ]) ?>

                <?php echo $this->render('user_account', [
                        'user' => $user
                ])?>
                <?php \yii\widgets\Pjax::end() ?>

            </div>
        </div>
    </div>
</div>



