<?php

use yii\db\Migration;

/**
 * Handles the creation of table `question`.
 */
class m170212_113252_create_question_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%question}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull()->defaultValue(1),
            'title' => $this->string(100)->notNull(),
            'content' => $this->text()->notNull(),
            'image' => $this->string(255),
            'qq_group' => $this->string(15),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%question}}');
    }
}
