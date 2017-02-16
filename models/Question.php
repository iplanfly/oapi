<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%question}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $content
 * @property string $image
 * @property string $qq_group
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Question extends ActiveRecord
{
    const TYPE_ALL = 100;
    const TYPE_PHP = 1;
    const TYPE_IOS = 2;
    const TYPE_ANDROID = 3;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['type', 'title', 'content'], 'required'],
            [['content'], 'string'],
            ['title', 'string', 'max' => 100],
            ['image', 'string', 'max' => 255],
            [['qq_group'], 'string', 'max' => 15],
            ['type', 'in', 'range' => [self::TYPE_ALL, self::TYPE_PHP, self::TYPE_IOS, self::TYPE_ANDROID]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'title' => '标题',
            'content' => '内容',
            'image' => '图片',
            'qq_group' => 'QQ群',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 获取 type
     */
    public function getType()
    {
        $types = [
            self::TYPE_ALL => '全部',
            self::TYPE_PHP => 'PHP',
            self::TYPE_IOS => 'iOS',
            self::TYPE_ANDROID => 'Android',
        ];

        return $types[$this->type];
    }
}
