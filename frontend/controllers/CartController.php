<?php


namespace frontend\controllers;


use common\models\Cart;
use common\models\Product;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class CartController extends \frontend\base\Controller
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
//             os items do carrinho vao ser armazenados pela visita
        }else{
            $cart = Cart::findBySql("
                              SELECT 
                              c.product_id as id, 
                              p.image,
                              p.name,
                              p.price, 
                              c.quantity, 
                              p.price * c.quantity as total_price 
                              FROM cart c
            LEFT JOIN products p on p.id = c.product_id 
                              WHERE  c.created_by = :userId", ['userId' => \Yii::$app->user->id
            ])
                ->asArray()
                ->all();
        }

        return $this->render('index', [
            'items' =>$cart
        ]);
    }

    public function actionAdd()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }

        if (\Yii::$app->user->isGuest) {
            //salva na sessao

        }else{
            $userId = \Yii::$app->user->id;
            $cart = Cart::find()->userId($userId)->productId($id)->one();

            if($cart) {
                $cart->quantity++;
            }else {

                $cart = new Cart();
                $cart->product_id = $id;
                $cart->created_by = $userId;
                $cart->quantity = 1;
            }

            if($cart->save()){
                return [
                    'success'=>true
                    ];
            }else {
                return[
                'success' =>false,
                    'errors' => $cart->errors
                ];
            }
        }
    }
}