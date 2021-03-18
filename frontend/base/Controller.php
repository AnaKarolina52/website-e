<?php


namespace frontend\base;


use common\models\Cart;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {

        $this->view->params['cartItemCount'] = Cart::findBySql(
            "SELECT SUM(quantity) FROM cart WHERE created_by = :userId", ['userId' => \Yii::$app->user->id]
        )-> scalar();
        return parent::beforeAction($action);
    }
}