<?php

/** @var \common\models\Product $model */
?>


<div class="card h-100">
    <a href="#" class="img-wrapper">
        <img class="card-img-top" src="<?php echo $model->getImageUrl() ?>" alt=""></a>
    <div class="card-body">
        <h4 class="card-title">
            <a href="#"><?php echo $model->name ?></a>
        </h4>
<!--        formating the currancy-->
        <h5><?php echo Yii::$app->formatter->asCurrency($model->price) ?></h5>
        <div class="card-text">
<!--            limitting the text-->
            <?php echo $model->getShortDescription() ?>
        </div>
    </div>
    <div class="card-footer text-right">
        <a href="<?php echo \yii\helpers\Url::to(['/basket/add']) ?>" class="btn btn-primary btn-add-to-basket">
            Add to Basket
        </a>
    </div>
</div>

