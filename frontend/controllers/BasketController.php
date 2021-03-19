<?php


namespace frontend\controllers;


use common\models\BasketItem;
use common\models\Product;
use frontend\base\Controller;
use yii\filters\ContentNegotiator;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BasketController extends \frontend\base\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add'],
                'formats'=> [
                    'application/json'=> Response::FORMAT_JSON,

                ],
            ]
        ];
    }
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest){
            //TODO Quando o usuario nao tem cadastro
        }else{
            $basketItems = BasketItem::findBySql("
                                        SELECT
                                        b.product_id as id,
                                        p.image,
                                        p.name,
                                        p.price,
                                        b.quantity,
                                        p.price * b.quantity as total_price
                                   FROM basket_items b
                                        LEFT JOIN products p on p.id = b.product_id
                                   WHERE b.created_by = :userId", [
                                       'userId'=>\Yii::$app->user->id])
                                        ->asArray()
                                        ->all();
        }

        return $this->render('index', [
            'items' =>$basketItems
        ]);
    }
    public function actionAdd()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }
        if (\Yii::$app->user->isGuest)
        {
            //vai ser salvo por entrada
        }else{

            $userId = \Yii::$app->user->id;
            $basketItem = BasketItem::find()->userId($userId)->productId($id)->one();
            if($basketItem){
                $basketItem->quantity++;
            }else {

                $basketItem = new BasketItem();
                $basketItem->product_id = $id;
                $basketItem->created_by = \Yii::$app->user->id;
                $basketItem->quantity = 1;
            }
            if ($basketItem->save()){
                return [
                    'success' => true
                ];
            }else{
                return [
                    'success' => false,
                    'errors' => $basketItem->errors
                ];
            }
        }
    }
}