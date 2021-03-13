<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders_products}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%products}}`
 * - `{{%orders}}`
 */
class m210313_163218_create_orders_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orders_products}}', [
            'id' => $this->primaryKey(),
            'product_name' => $this->string(255)->notNull(),
            'product_id' => $this->integer(11)->notNull(),
            'unit_price' => $this->decimal(10,2)->notNull(),
            'order_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(2)->notNull(),
        ]);


        // creates index for column `product_id`
        $this->createIndex(
            '{{%idx-orders_products-product_id}}',
            '{{%orders_products}}',
            'product_id'
        );

        // add foreign key for table `{{%products}}`
        $this->addForeignKey(
            '{{%fk-orders_products-product_id}}',
            '{{%orders_products}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // creates index for column `order_id`
        $this->createIndex(
            '{{%idx-orders_products-order_id}}',
            '{{%orders_products}}',
            'order_id'
        );

        // add foreign key for table `{{%orders}}`
        $this->addForeignKey(
            '{{%fk-orders_products-order_id}}',
            '{{%orders_products}}',
            'order_id',
            '{{%orders}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%products}}`
        $this->dropForeignKey(
            '{{%fk-orders_products-product_id}}',
            '{{%orders_products}}'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            '{{%idx-orders_products-product_id}}',
            '{{%orders_products}}'
        );

        // drops foreign key for table `{{%orders}}`
        $this->dropForeignKey(
            '{{%fk-orders_products-order_id}}',
            '{{%orders_products}}'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            '{{%idx-orders_products-order_id}}',
            '{{%orders_products}}'
        );

        $this->dropTable('{{%orders_products}}');
    }
}
