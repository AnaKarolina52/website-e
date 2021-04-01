<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%order_locations}}".
 *
 * @property int $order_id
 * @property string $address
 * @property string $city
 * @property string $country
 * @property string $county
 * @property string|null $postcode
 *
 * @property Order $order
 */
class OrderLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_locations}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'address', 'city', 'country', 'county'], 'required'],
            [['order_id'], 'integer'],
            [['address', 'city', 'country', 'county', 'postcode'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
            'county' => 'County',
            'postcode' => 'Postcode',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderLocationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderLocationQuery(get_called_class());
    }
}
