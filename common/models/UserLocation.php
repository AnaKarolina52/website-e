<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_locations}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $county
 * @property string|null $zipcode
 *
 * @property User $user
 */
class UserLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_locations}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'address', 'city', 'state', 'county'], 'required'],
            [['user_id'], 'integer'],
            [['address', 'city', 'state', 'county', 'zipcode'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'county' => 'County',
            'zipcode' => 'Zipcode',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\UserLocationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserLocationQuery(get_called_class());
    }
}
