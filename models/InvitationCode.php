<?php

namespace app\models;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "{{%invitation_code}}".
 *
 * @property integer $id
 * @property string $invitation_code
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $status
 * @property integer $used_at
 * @property integer $used_by
 */
class InvitationCode extends \yii\db\ActiveRecord
{
    const CREATED_BY_CONSOLE = 0;

    const STATUS_ACTIVE = 10;
    const STATUS_USED = 11;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invitation_code}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invitation_code', 'created_at', 'created_by'], 'required'],
            [['created_at', 'created_by', 'status', 'used_at', 'used_by'], 'integer'],
            [['invitation_code'], 'string', 'max' => 8],
            [['invitation_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invitation_code' => 'Invitation Code',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'status' => 'Status',
            'used_at' => 'Used At',
            'used_by' => 'Used By',
        ];
    }

    /**
     * 使用邀请码 (注册)
     *
     * @param $user_id 使用者ID
     * @throws yii\web\ServerErrorHttpException;
     */
    public function use($user_id)
    {
        $this->status = self::STATUS_USED;
        $this->used_by = $user_id;
        $this->used_at = time();

        if (!$this->save()) {
            throw new ServerErrorHttpException('验证码无法使用！');
        }
    }
}
