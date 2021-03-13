<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_locations}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m210313_161811_create_user_locations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_locations}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'address' => $this->string(255)->notNull(),
            'city' => $this->string(255)->notNull(),
            'state' => $this->string(255)->notNull(),
            'county' => $this->string(255)->notNull(),
            'zipcode' => $this->string(255),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_locations-user_id}}',
            '{{%user_locations}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_locations-user_id}}',
            '{{%user_locations}}',
            'user_id',
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
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_locations-user_id}}',
            '{{%user_locations}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_locations-user_id}}',
            '{{%user_locations}}'
        );

        $this->dropTable('{{%user_locations}}');
    }
}
