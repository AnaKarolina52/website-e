<?php


namespace frontend\controllers;


use common\models\BasketItem;
use common\models\Product;
use frontend\base\Controller;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
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
            ],
            [
                'class' => VerbFilter::class,
                'actions'=> ['POST', 'DELETE'],
            ]
        ];
    }
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest){
            // Quando o usuario nao tem cadastro
            $basketItems = \Yii::$app->session->get(BasketItem::SESSION_KEY, []);


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

        {   //search the register if found do it update the quantity if not create a new basket set in basket and in the session
            $basketItems = \Yii::$app->session->get(BasketItem::SESSION_KEY, []);
            $found = false;
            foreach ($basketItems as &$basketItem){
                if($basketItem['id'] == $id){
                    $basketItem['quantity']++;
                    $found = true;
                    break;
                }
            }
            if (!$found){
                    $basketItem = [
                    'id' => $id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'quantity' => 1,
                    'total_price' => $product->price
                ];

                $basketItems[] = $basketItem;
            }

           \Yii::$app->session->set(BasketItem::SESSION_KEY, $basketItems);

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

    public function actionDelete($id)
    {
        if (isGuest()) {
            $basketItems = \Yii::$app->session->get(BasketItem::SESSION_KEY, []);
            foreach ($basketItems as $i => $basketItem) {
                if($basketItem['id'] == $id){
                array_splice($basketItems, $i, 1);
                break;
            }
          }
         \Yii::$app->session->set(BasketItem::SESSION_KEY, $basketItems);
        }else{
            BasketItem::deleteAll(['product_id' =>$id, 'created_by' => currUserId()]);
        }
        return $this->redirect(['index']);
    }
}