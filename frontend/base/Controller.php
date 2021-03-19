<?php


namespace frontend\base;


use common\models\BasketItem;


class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
   {
      $this->view->params['basketItemCount']= BasketItem::findBySql("
    
            SELECT SUM(quantity) FROM basket_items WHERE created_by = :userId", ['userId' => \Yii::$app->user->id]
      )->scalar();
      return parent::beforeAction($action);
   }


}