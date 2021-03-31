<?php
namespace common\models;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\debug\panels\DumpPanel;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property float $price
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *@property BasketItem[] $basketItems
 * @property OrdersProduct[] $ordersProducts
 * @property User $createdBy
 * @property User $updatedBy
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @var yii\web\UploadedFile;
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'status'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 2000],
            [['imageFile'],'image','extensions'=>'png, jpg, jpeg, webp', 'maxSize'=> 10 * 1024 * 1024],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Product Image',
            'imageFile'=> 'Product Image',
            'price' => 'Price',
            'status' => 'Published',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[BasketItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\BasketItemQuery
     */
    public function getBasketItems()
    {
        return $this->hasMany(BasketItem::className(), ['product_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersProducts]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrdersProductQuery
     */
    public function getOrdersProducts()
    {
        return $this->hasMany(OrdersProduct::className(), ['product_id' => 'id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ProductQuery the active query used by this AR class.
     */

    public static function find()
    {
        return new \common\models\query\ProductQuery(get_called_class());
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->imageFile){
            $this->image = '/products/'.Yii::$app->security->generateRandomString().'/'.$this->imageFile->name;
        }

        $transaction =Yii::$app->db->beginTransaction();
        $ready = parent::save($runValidation, $attributeNames);

        if ($ready && $this->imageFile){
            $fullPath = Yii::getAlias('@frontend/web/storage'.$this->image);

            $dir = dirname($fullPath);
            if (!FileHelper::createDirectory($dir) | !$this->imageFile->saveAs($fullPath)){
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return $ready;
    }

//    aqui vamos fazer com que tenhamos acesso a imagem tanto quando temos a imagem especifica ate qdo nao temos imagem
    public function getImageUrl()
    {
        return self::formatImageUrl($this->image);
    }

    public static function formatImageUrl($imagePath)
    {
        if($imagePath){
            return Yii::$app->params['frontendUrl'].'/storage'.$imagePath;

    }
// caso nao seja selecionado uma imagem estabelecemos uma imagem padrao que vai aparecer qdo nao e encontrada imagem no produto
    return Yii::$app->params['frontendUrl'].'/img/no-image-available.jpg';
}
//delimitar o tamanho do texto que aparece sobre o produto
    public function getShortDescription()
    {
        return \yii\helpers\StringHelper::truncateWords(strip_tags($this->description), 30);
    }
//remove the product without take the product to the view order
    public function afterDelete()
    {
        parent::afterDelete();
        if($this->image){
            $dir = Yii::getAlias('@frontend/web/storage').dirname($this->image);
            FileHelper::removeDirectory($dir);
        }
    }

    public function delete()
    {
        Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS = 0;");
        return parent::delete();
    }
}
