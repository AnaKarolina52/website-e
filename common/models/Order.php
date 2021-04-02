<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property float $total_price
 * @property int $status
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string|null $transaction_id
 * @property string|null $paypal_order_id
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property OrderLocation[] $orderLocation
 * @property User $createdBy
 * @property OrdersProduct[] $ordersProducts
 */
class Order extends \yii\db\ActiveRecord
{
    const  STATUS_UNPAID = 0;
    const  STATUS_PAID = 1;
    const  STATUS_FAILED = 2;
    const  STATUS_COMPLETED = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price', 'status', 'firstname', 'lastname', 'email'], 'required'],
            [['total_price'], 'number'],
            [['email'], 'email'],
            [['status', 'created_at', 'created_by'], 'integer'],
            [['firstname', 'lastname'], 'string', 'max' => 45],
            [['email', 'transaction_id', 'paypal_order_id'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'transaction_id' => 'Transaction ID',
            'paypal_order_id' => 'Paypal Order ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[OrderLocations]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderLocationQuery
     */
    public function getOrderLocation()
    {
        return $this->hasOne(OrderLocation::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[OrdersProducts]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrdersProductQuery
     */
    public function getOrdersProducts()
    {
        return $this->hasMany(OrdersProduct::className(), ['order_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
        public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
    }

    public function saveLocation($postData)
    {
        $orderLocation = new OrderLocation();
        $orderLocation->order_id = $this->id;
        if($orderLocation->load($postData) && $orderLocation->save()){
            return true;
        }
        throw new Exception("Was not possible save order location: ".implode('<br>', $orderLocation->getFirstErrors()));
    }

    public function saveOrdersProducts()
    {
        $basketItems = BasketItem::getItemsForUser(currUserId());
        foreach ($basketItems as $basketItem) {
            $ordersProduct = new OrdersProduct();
            $ordersProduct ->product_name = $basketItem['name'];
            $ordersProduct ->product_id = $basketItem['id'];
            $ordersProduct ->unit_price = $basketItem['price'];
            $ordersProduct ->order_id = $this->id;
            $ordersProduct ->quantity = $basketItem['quantity'];
            if (!$ordersProduct->save()) {
               throw new Exception("Order Product was not saved: ".implode('<br>',$ordersProduct->getFirstError()));
            }
        }

        return true;
    }

    public function getItemsQuantity()
    {
        return $sum = BasketItem::findBySql("
            SELECT SUM(quantity) FROM orders_products WHERE order_id = :orderId", ['orderId' => $this->id]
        )->scalar();
    }

    public function sendEmailToSeller()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'order_completed_seller-html', 'text' => 'order_completed_seller-text'],
                ['order' => $this]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo(Yii::$app->params['sellerEmail'])
            ->setSubject('New order has been made at ' . Yii::$app->name)
            ->send();
    }

    public function sendEmailToClient()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'order_completed_client-html', 'text' => 'order_completed_client-text'],
                ['order' => $this]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Your order is confirmed at ' . Yii::$app->name)
            ->send();
    }
    public static function getTheStatus()
    {
        return [
            self::STATUS_PAID =>'Paid',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_UNPAID => 'Unpaid'
            ];
    }
}
