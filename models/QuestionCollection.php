<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%question_collection}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $question_id
 * @property integer $created_at
 */
class QuestionCollection extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question_collection}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'question_id'], 'required'],
            [['user_id', 'question_id', 'created_at'], 'integer'],
            ['question_id', 'unique', 'filter' => ['user_id' => Yii::$app->user->id], 'message' => '您已经收藏了此问题了~'],
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
            'question_id' => 'Question ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 获取用户收藏的问题ID
     *
     * @param integer $user_id 用户ID
     * @return array
     */
    public static function getQuestionId($user_id)
    {
        return static::find()
            ->select(['question_id'])
            ->where(['user_id' => $user_id])
            ->asArray()
            ->column();
    }
}
