<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\BasketItem]].
 *
 * @see \common\models\BasketItem
 */
class BasketItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\BasketItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\BasketItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $userId
     * @return BasketItemQuery
     */
    public function userId($userId)
    {
        return $this->andWhere(['created_by' => $userId]);
    }

    public function productId($productId)
    {
        return $this->andWhere(['product_id' => $productId]);
    }
}
