<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\QueryParamAuth;
use app\models\Signup;
use app\models\Login;
use app\models\UpdatePassword;
use yii\filters\VerbFilter;

/**
 * 用户控制器
 *
 * @author piaoyii <sky@piaoyii.com>
 */
class UserController extends \yii\rest\Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
	    $behaviors = parent::behaviors();
	    $behaviors['authenticator'] = [
	        'class' => QueryParamAuth::className(),
			'except' => ['signup', 'login'],
	    ];
	    $behaviors['verbs'] = [
    	    'class' => VerbFilter::className(),
            'actions' => [
                'signup' => ['post'],
                'login' => ['post'],
                'update-password' => ['post'],
            ],
	    ];
	    return $behaviors;
	}

	/**
	 * 注册
	 *
	 * @return string access_token.
	 */
	public function actionSignup()
	{
		$model = new Signup;

		$model->attributes = Yii::$app->request->post();

		return $model->signup();
	}

	/**
	 * 登录
	 * 
	 * @return string access_token.
	 */
	public function actionLogin()
	{
        $model = new Login();
        $model->attributes = Yii::$app->request->post();

        return $model->login();
	}

	/**
	 * 修改密码。
	 *
	 * 在用户登录的情况下修改密码
	 */
	public function actionUpdatePassword()
	{
		$model = new UpdatePassword();
		$model->attributes = Yii::$app->request->post();

		return $model->updatePassword();
	}
}
