

<?php

/** @var \common\models\Order  $order*/
/** @var \common\models\OrderLocation  $orderLocation*/
/** @var array $basketItems  */
/** @var int $productQuantity  */
/** @var float $totalPrice  */

use yii\bootstrap4\ActiveForm;

?>




<?php $form = ActiveForm::begin([
    'action'=> [''],
    ]); ?>

<div class="row">
    <div class="col">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($order, 'lastname')->textInput(['autofocus' => true]) ?>
                    </div>
                </div>
                <?= $form->field($order, 'email')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
           <div class="card">
            <div class="card-header">
               <h5> My Account</h5>
            </div>
            <div class="card-body">
                <?= $form->field($orderLocation, 'address') ?>
                <?= $form->field($orderLocation, 'city') ?>
                <?= $form->field($orderLocation, 'state') ?>
                <?= $form->field($orderLocation, 'county') ?>
                <?= $form->field($orderLocation, 'zipcode') ?>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card">
            <div class="card-header">
                <h4>Overview of your order</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td colspan="2"><?php echo $productQuantity ?> Products</td>
                    </tr>
                    <tr>
                        <td>Total Price</td>
                        <td class="text-right">
                            <?php echo Yii::$app->formatter->asCurrency($totalPrice) ?>
                        </td>
                    </tr>
                </table>
                <p class="text-right">
                    <button class="btn btn-success m-3">Checkout</button>
                </p>
            </div>
        </div>
    </div>
</div>



<?php ActiveForm::end(); ?>
