<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

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

    /**
     * 删除收藏的问题
     *
     * @param array $questionId 问题ID
     * @throws yii\web\ForbiddenHttpException;
     */
    public static function deleteCollectedQuestion(array $questionId)
    {
        foreach ($questionId as $id) {
            static::getModel($id)->delete();
        }
    }

    /**
     * 获取指定用户的指定问题的模型
     *
     * @param $questionId 问题ID
     * @return model
     * @throws yii\web\ForbiddenHttpException if the model is null
     */
    public static function getModel($questionId)
    {
        $model = static::findOne(['user_id' => Yii::$app->user->id, 'question_id' => $questionId]);

        if ($model === null) {
            throw new ForbiddenHttpException('并没有收藏这个问题');
        }

        return $model;
    }

    /**
     * 判断一个用户的一个问题是否已收藏
     *
     * @param integer $userId 用户ID
     * @param integer $questionId 问题ID
     */
    public static function isCollected($userId, $questionId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'question_id' => $questionId])
            ->exists();
    }
}
