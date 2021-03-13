<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_locations}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%orders}}`
 */
class m210313_162804_create_order_locations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_locations}}', [

            'order_id' => $this->integer()->notNull(),
            'address' => $this->string(255)->notNull(),
            'city' => $this->string(255)->notNull(),
            'state' => $this->string(255)->notNull(),
            'county' => $this->string(255)->notNull(),
            'zipcode' => $this->string(255),
        ]);

        $this->addPrimaryKey('PK_order_locations', '{{%order_locations}}', 'order_id');

        // creates index for column `order_id`
        $this->createIndex(
            '{{%idx-order_locations-order_id}}',
            '{{%order_locations}}',
            'order_id'
        );

        // add foreign key for table `{{%orders}}`
        $this->addForeignKey(
            '{{%fk-order_locations-order_id}}',
            '{{%order_locations}}',
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
        // drops foreign key for table `{{%orders}}`
        $this->dropForeignKey(
            '{{%fk-order_locations-order_id}}',
            '{{%order_locations}}'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            '{{%idx-order_locations-order_id}}',
            '{{%order_locations}}'
        );

        $this->dropTable('{{%order_locations}}');
    }
}
