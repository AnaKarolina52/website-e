<?php


namespace frontend\base;


use common\models\BasketItem;


class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
   {

      $this->view->params['basketItemCount']= BasketItem::getTotalQuantityForUser(currUserId());
      return parent::beforeAction($action);
   }
}