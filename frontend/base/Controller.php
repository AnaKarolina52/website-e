<?php


namespace frontend\base;


use common\models\BasketItem;


class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
   {
       if (\Yii::$app->user->isGuest) {
           $basketItems = \Yii::$app->session->get(BasketItem::SESSION_KEY, []);
           $sum = 0;
           foreach ($basketItems as $basketItem){
               $sum +=$basketItem['quantity'];
           }
       }else {
           //if is not a guest
           $sum = BasketItem::findBySql("
            SELECT SUM(quantity) FROM basket_items WHERE created_by = :userId", ['userId' => \Yii::$app->user->id]
           )->scalar();
       }
      $this->view->params['basketItemCount']= $sum;
      return parent::beforeAction($action);
   }
}