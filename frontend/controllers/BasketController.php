<?php

namespace frontend\controllers;


use common\models\BasketItem;
use common\models\Order;
use common\models\OrderLocation;
use common\models\Product;
use frontend\base\Controller;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsGetRequest;
use Sample\PayPalClient;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class BasketController extends \frontend\base\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add', 'create-order', 'submit-payment', 'change-quantity'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,

                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                    'create-order' => ['POST'],
                ]
            ]
        ];
    }

    public function actionIndex()

    {
        $basketItems = BasketItem::getItemsForUser(currUserId());

        return $this->render('index', [
            'items' => $basketItems
        ]);
    }


    public function actionAdd()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }

        if (\Yii::$app->user->isGuest) {   //search the register if found do it update the quantity if not create a new basket set in basket and in the session
            $basketItems = \Yii::$app->session->get(BasketItem::SESSION_KEY, []);
            $found = false;
            foreach ($basketItems as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
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

        } else {

            $userId = \Yii::$app->user->id;
            $basketItem = BasketItem::find()->userId($userId)->productId($id)->one();
            if ($basketItem) {
                $basketItem->quantity++;
            } else {

                $basketItem = new BasketItem();
                $basketItem->product_id = $id;
                $basketItem->created_by = \Yii::$app->user->id;
                $basketItem->quantity = 1;
            }
            if ($basketItem->save()) {
                return [
                    'success' => true
                ];
            } else {
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
                if ($basketItem['id'] == $id) {
                    array_splice($basketItems, $i, 1);
                    break;
                }
            }
            \Yii::$app->session->set(BasketItem::SESSION_KEY, $basketItems);
        } else {
            BasketItem::deleteAll(['product_id' => $id, 'created_by' => currUserId()]);
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


        if (isGuest()) {
            $basketItems = \Yii::$app->session->get(BasketItem::SESSION_KEY, []);
            foreach ($basketItems as &$basketItem) {
                if ($basketItem['id'] === $id) {
                    $basketItem['quantity'] = $quantity;
                    break;
                }
            }
            \Yii::$app->session->set(BasketItem::SESSION_KEY, $basketItems);
        } else {
            $basketItem = BasketItem::find()->userId(currUserId())->productId($id)->one();
            if ($basketItem) {
                $basketItem->quantity = $quantity;
                $basketItem->save();
            }
        }

        return [
            'quantity' => BasketItem::getTotalQuantityForUser(currUserId()),
            'price' => Yii::$app->formatter->asCurrency(BasketItem::getTotalPriceForItemForUser($id, currUserId()))
        ];
    }

    public function actionCheckout()
    {
        $basketItems = BasketItem::getItemsForUser(currUserId());
        $productQuantity = BasketItem::getTotalQuantityForUser(currUserId());
        $totalPrice = BasketItem::getTotalPriceForUser(currUserId());

        if (empty($basketItems)) {
            return $this->redirect([Yii::$app->homeUrl]);
        }
        $order = new Order();

        $order->total_price = $totalPrice;
        $order->status = Order::STATUS_UNPAID;
        $order->created_at = time();
        $order->created_by = currUserId();
        $transaction = Yii::$app->db->beginTransaction();
        if ($order->load(Yii::$app->request->post())
            && $order->save()
            && $order->saveLocation(Yii::$app->request->post())
            && $order->saveOrdersProducts()) {
            $transaction->commit();

            BasketItem::clearBasketItems(currUserId());

            return $this->render('pay-now', [
                'order' => $order,
            ]);
        }

        $orderLocation = new OrderLocation();
        if (!isGuest()) {
            /** @var  \common\models\User $user */
            $user = \Yii::$app->user->identity;
            $userLocation = $user->getLocation();

            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;
            $order->status = Order::STATUS_UNPAID;

            $orderLocation->address = $userLocation->address;
            $orderLocation->city = $userLocation->city;
            $orderLocation->state = $userLocation->state;
            $orderLocation->county = $userLocation->county;
            $orderLocation->zipcode = $userLocation->zipcode;
        }
        return $this->render('checkout', [
            'order' => $order,
            'orderLocation' => $orderLocation,
            'basketItems' => $basketItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }

    public function actionSubmitPayment($orderId)
    {
        $where = ['id' => $orderId, 'status' => Order::STATUS_UNPAID];
        if (!isGuest()) {
            $where['created_by'] = currUserId();
        }
        $order = Order::findOne($where);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        $req = Yii::$app->request;
        $paypalOrderId = $req->post('orderId');
        $exists = Order::find()->andWhere(['paypal_order_id' => $paypalOrderId])->exists();
        if ($exists) {
            throw new BadRequestHttpException();
        }

        $environment = new SandboxEnvironment(Yii::$app->params['paypalClientId'], Yii::$app->params['paypalSecret']);
        $client = new PayPalHttpClient($environment);

        $response = $client->execute(new OrdersGetRequest($paypalOrderId));

        //preciso salvar response information in logs
        if ($response->statusCode === 200) {
            $order->paypal_order_id = $paypalOrderId;
            $paidAmount = 0;
            foreach ($response->result->purchase_units as $purchase_unit) {
                if ($purchase_unit->amount->currency_code === 'USD') {
                    $paidAmount += $purchase_unit->amount->value;
                }
            }
            if ($paidAmount === (float)$order->total_price && $response->result->status === 'COMPLETED') {
                $order->status = Order::STATUS_PAID;
            }
            $order->transaction_id = $response->result->purchase_units[0]->payments->captures[0]->id;

            if ($order->save()) {
                if (!$order->sendEmailToSeller()) {
                    Yii::error("Seller email was not send");
                }
                if (!$order->sendEmailToClient()) {
                    Yii::error("Client email was not send");
                }

                return [
                    'success' => true
                ];
            } else {
                Yii::error("Order wasn't saved.Data: " . VarDumper::dumpAsString($order->toArray()) .
                    '.Errors: ' . VarDumper::dumpAsString($order->errors));

            }
        }

        throw new BadRequestHttpException();


    }
}