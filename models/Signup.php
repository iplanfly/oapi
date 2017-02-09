<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\InvitationCode;
use yii\web\ForbiddenHttpException;

/**
 * Signup model
 */
class Signup extends Model
{
    public $mobilePhone;
    public $password;
    public $passwordRepeat;
    public $invitationCode;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobilePhone', 'password', 'passwordRepeat', 'invitationCode'], 'required'],
            ['mobilePhone', 'number', 'numberPattern' => '/^1\d{10}$/', 'message' => '手机号格式不正确！'],
            ['mobilePhone', 'unique', 'targetClass' => '\app\models\User', 'targetAttribute' => 'mobile_phone', 'message' => '手机号已被注册！'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => '两次输入的密码必须一致！'],
            ['invitationCode', 'exist', 'targetClass' => '\app\models\InvitationCode', 'targetAttribute' => 'invitation_code', 'filter' => ['status' => InvitationCode::STATUS_ACTIVE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobilePhone' => '手机号',
            'password' => '密码',
            'passwordRepeat' => '重复密码',
            'invitationCode' => '邀请码',
        ];
    }

    /**
     * Signs user up.
     *
     * @throws yii\web\ForbiddenHttpException.
     * @return string access_token.
     */
    public function signup()
    {
        $transaction = Yii::$app->db->beginTransaction();

        if (!$this->validate()) {
            $errors = $this->getFirstErrors();
            throw new ForbiddenHttpException(reset($errors));
            //return [key($errors) => reset($errors)];
        }
        
        $user = new User();
        $user->mobile_phone = $this->mobilePhone;
        $user->setPassword($this->password);
        $user->generateAccessToken();

        $user->save();

        $user->useInvitationCode($this->invitationCode);
        
        $transaction->commit();
        return $user->access_token;
    }
}
