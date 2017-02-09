<?php

use yii\db\Migration;

class m170206_071016_cteate_invitation_code_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%invitation_code}}', [
            'id' => $this->primaryKey(),
            'invitation_code' => $this->string(8)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'used_at' => $this->integer(),
            'used_by' => $this->integer(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }
    
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%invitation_code}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
