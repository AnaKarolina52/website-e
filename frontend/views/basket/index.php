
<?php
/** @var array $items */

?>

<div class="card">
    <div class="card-header">
        <h3>Shopping Basket</h3>
    </div>
    <div class="card-body p-0">

        <?php if(!empty($items)): ?>
        <table class = "table table-hover">
            <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr data-id="<?php echo $item['id'] ?>" data-url="<?php echo \yii\helpers\Url::to(['/basket/change-quantity']) ?>">
                    <td><?php echo $item['name']?></td>
                    <td>
                        <img src="<?php echo \common\models\Product::formatImageUrl($item['image'])  ?>"
                             style=" width: 50px;"
                             alt="<?php echo $item['name'] ?>">
                    </td>
                    <td><?php echo $item['price']?></td>
                    <td>
                        <input type="number" min="1" class="form-control item-quantity" style="width: 60px" value="<?php echo $item['quantity'] ?>">
                    </td>
                    <td><?php echo $item['total_price']?></td>
                    <td>
                        <?php echo \yii\helpers\Html::a('Remove', ['/basket/delete', 'id' =>$item['id']], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to remove this item?'
                        ]) ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="card-body text-right">
            <a href="<?php echo yii\helpers\Url::to(['/basket/checkout']) ?>" class="btn btn-primary">Checkout</a>
        </div>
        <?php else: ?>

        <p class="text-muted text-center p-5">There are no items in the basket</p>

    <?php endif; ?>
    </div>
</div>

