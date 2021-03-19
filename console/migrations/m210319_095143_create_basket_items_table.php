<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%basket_items}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%products}}`
 * - `{{%user}}`
 */
class m210319_095143_create_basket_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%basket_items}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(2)->notNull(),
            'created_by' => $this->integer(11),
        ]);

        // creates index for column `product_id`
        $this->createIndex(
            '{{%idx-basket_items-product_id}}',
            '{{%basket_items}}',
            'product_id'
        );

        // add foreign key for table `{{%products}}`
        $this->addForeignKey(
            '{{%fk-basket_items-product_id}}',
            '{{%basket_items}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // creates index for column `created_by`
        $this->createIndex(
            '{{%idx-basket_items-created_by}}',
            '{{%basket_items}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-basket_items-created_by}}',
            '{{%basket_items}}',
            'created_by',
            '{{%user}}',
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
            '{{%fk-basket_items-product_id}}',
            '{{%basket_items}}'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            '{{%idx-basket_items-product_id}}',
            '{{%basket_items}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-basket_items-created_by}}',
            '{{%basket_items}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-basket_items-created_by}}',
            '{{%basket_items}}'
        );

        $this->dropTable('{{%basket_items}}');
    }
}
