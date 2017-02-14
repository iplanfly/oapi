<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_info`.
 */
class m170212_102454_create_user_info_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user_info}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'nickname' => $this->string(100)->unique(),
            'face' => $this->string(100),
            'qq' => $this->string(15),
            'wechat' => $this->string(60),
            'qq_group' => $this->string(15), //QQ群
            'intro' => $this->string(60),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%user_info}}');
    }
}
