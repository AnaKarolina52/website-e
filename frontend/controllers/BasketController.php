<?php



namespace frontend\controllers;



use common\models\BasketItem;
use common\models\Order;
use common\models\OrderLocation;
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
            $basketItems = BasketItem::getItemsForUser(currUserId());
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
            foreach ($basketItems as &$item){
                if($item['id'] == $id){
                    $item['quantity']++;
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

    public function actionChangeQuantity()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }

        $quantity = \Yii::$app->request->post('quantity');


        if(isGuest()) {
            $basketItems =\Yii::$app->session->get(BasketItem::SESSION_KEY, []);
            foreach ($basketItems as &$basketItem) {
                if ($basketItem['id'] === $id) {
                    $basketItem['quantity'] = $quantity;
                    break;
                }
            }
            \Yii::$app->session->set(BasketItem::SESSION_KEY, $basketItems);
        }else {
                    $basketItem = BasketItem::find()->userId(currUserId())->productId($id)->one();
                    if ($basketItem){
                        $basketItem->quantity = $quantity;
                        $basketItem->save();
          }
       }

        return BasketItem::getTotalQuantityForUser(currUserId());
    }

    public function actionCheckout()
    {
        $order = new Order();
        $orderLocation = new OrderLocation();
        if (!isGuest()) {
            /** @var  \common\models\User $user */
            $user = \Yii::$app->user->identity;
            $userLocation = $user->getLocation();

            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;
            $order->status = Order::STATUS_DRAFT;


            $orderLocation->address = $userLocation->address;
            $orderLocation->city = $userLocation->city;
            $orderLocation->state = $userLocation->state;
            $orderLocation->county = $userLocation->county;
            $orderLocation->zipcode = $userLocation->zipcode;
            $basketItems = BasketItem::getItemsForUser(currUserId());
        }else{
            $basketItems =\Yii::$app->session->get(BasketItem::SESSION_KEY, []);
        }

        $productQuantity =BasketItem::getTotalQuantityForUser(currUserId());
        $totalPrice = BasketItem::getTotalPriceForUser(currUserId());

        return $this->render('checkout', [
            'order' => $order,
            'orderLocation' => $orderLocation,
            'basketItems' => $basketItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice

        ]);
    }
}