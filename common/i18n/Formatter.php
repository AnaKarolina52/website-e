<?php

namespace common\i18n;


/** @package common\i18n */

class Formatter extends \yii\i18n\Formatter
{
    public function asOrderStatus($status)
    {
        if($status == \common\models\Order::STATUS_COMPLETED){
            return \yii\bootstrap4\Html::tag('span', 'Completed', ['class'=>'badge badge-success']);
        }elseif($status == \common\models\Order::STATUS_PAID){
            return \yii\bootstrap4\Html::tag('span', 'Paid', ['class'=>'badge badge-primary']);
        }elseif ($status == \common\models\Order::STATUS_UNPAID){
            return \yii\bootstrap4\Html::tag('span', 'Unpaid', ['class'=>'badge badge-secondary']);
        }else{
            return \yii\bootstrap4\Html::tag('span', 'Failured', ['class'=>'badge badge-danger']);
        }
    }

}