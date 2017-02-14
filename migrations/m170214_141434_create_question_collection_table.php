<?php

use yii\db\Migration;

/**
 * Handles the creation of table `question_collection`.
 */
class m170214_141434_create_question_collection_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%question_collection}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%question_collection}}');
    }
}
