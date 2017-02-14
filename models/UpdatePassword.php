<?php

namespace app\models;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * 修改密码模型
 */
class UpdatePassword extends \yii\base\Model
{
	public $passwordOld;
	public $passwordNew;
	public $passwordRepeat;

    private $_user;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['passwordOld', 'passwordNew', 'passwordRepeat'], 'required'],
			[['passwordOld', 'passwordNew', 'passwordRepeat'], 'string', 'min' => 6],
			['passwordRepeat', 'compare', 'compareAttribute' => 'passwordNew', 'message' => '两次输入的密码必须一致！'],
			['passwordOld', 'validatePassword'],
		];
	}

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->passwordOld)) {
                $this->addError($attribute, '原密码错误！');
            }
        }
    }

	/**
	 * 修改密码
     *
	 * @throws ForbiddenHttpException
     * @return true|false
	 */
	public function updatePassword()
	{
		if (!$this->validate()) {
            $errors = $this->getFirstErrors();
            throw new ForbiddenHttpException(reset($errors));
		}

		$user = $this->getUser();
		$user->setPassword($this->passwordNew);

		return $user->save();
	}

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(Yii::$app->user->id);
        }

        return $this->_user;
    }
}
