<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_info}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $nickname
 * @property string $face
 * @property string $qq
 * @property string $wechat
 * @property string $qq_group
 * @property string $intro
 */
class UserInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['nickname', 'face'], 'string', 'max' => 100],
            [['qq', 'qq_group'], 'string', 'max' => 15],
            [['wechat', 'intro'], 'string', 'max' => 60],
            [['nickname'], 'unique', 'message' => '昵称已被占用！'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'nickname' => '昵称',
            'face' => '头像',
            'qq' => 'QQ',
            'wechat' => '微信',
            'qq_group' => 'QQ群',
            'intro' => '介绍',
        ];
    }

    /**
     * 获取数据模型。
     *
     * 如果不存在，则新建。
     * 
     * @return object Model
     */
    public static function getModel()
    {
        if (($model = static::findOne(['user_id' =>Yii::$app->user->id])) === null) {
            $model = new static();
        }

        return $model;
    }
}
