<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cart}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%products}}`
 * - `{{%user}}`
 */
class m210313_163154_create_cart_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cart}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(2)->notNull(),
            'created_by' => $this->integer(11),
        ]);

        // creates index for column `product_id`
        $this->createIndex(
            '{{%idx-cart-product_id}}',
            '{{%cart}}',
            'product_id'
        );

        // add foreign key for table `{{%products}}`
        $this->addForeignKey(
            '{{%fk-cart-product_id}}',
            '{{%cart}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // creates index for column `created_by`
        $this->createIndex(
            '{{%idx-cart-created_by}}',
            '{{%cart}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-cart-created_by}}',
            '{{%cart}}',
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
            '{{%fk-cart-product_id}}',
            '{{%cart}}'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            '{{%idx-cart-product_id}}',
            '{{%cart}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-cart-created_by}}',
            '{{%cart}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-cart-created_by}}',
            '{{%cart}}'
        );

        $this->dropTable('{{%cart}}');
    }
}
