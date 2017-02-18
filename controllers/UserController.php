<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\QueryParamAuth;
use app\models\Signup;
use app\models\Login;
use app\models\UpdatePassword;
use yii\filters\VerbFilter;
use app\models\UserInfo;
use app\models\Upload;
use yii\web\ForbiddenHttpException;
use app\models\QuestionSearch;
use app\models\QuestionCollection;
use app\models\QuestionCollectedSearch;

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
                'info' => ['get'],
                'edit-info' => ['post'],
                'my-question' => ['get'],
                'my-collected-question' => ['get'],
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
		$model = new Signup();

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
     *
	 * @return true|string
	 */
	public function actionUpdatePassword()
	{
		$model = new UpdatePassword();
		$model->attributes = Yii::$app->request->post();

		return $model->updatePassword();
	}

	/**
	 * 获取个人信息
	 * 
	 * @return array
	 */
	public function actionInfo()
	{
		return UserInfo::getUserInfo();
	} 

	/**
	 * 编辑个人信息
	 *
	 * @throws ForbiddenHttpException
	 * @return boolern
	 */
	public function actionEditInfo()
	{
		$model = UserInfo::getModel();

		$model->attributes = Yii::$app->request->post();
		$model->qq_group = Yii::$app->request->post('qqGroup');
		$model->user_id = Yii::$app->user->id;

		$transaction = Yii::$app->db->beginTransaction();

		if (!$model->save()) {
            $errors = $model->getFirstErrors();
            throw new ForbiddenHttpException(reset($errors));
		}

		if (!empty($_FILES)) {

			$uploadModel = new Upload;
			$uploadModel->getMyInstance($_FILES['face']);
			$facePath = $uploadModel->uploadFace();
			$model->face = $facePath;
			$model->save();
		}

		$transaction->commit();

		return true;
	}

	/**
	 * 获取自己曾经发布的问题
	 *
	 * @return array
	 */
	public function actionMyQuestions()
	{
		$queryParams = Yii::$app->request->queryParams;
		$queryParams['QuestionSearch']['user_id'] = Yii::$app->user->id;

        $searchModel = new QuestionSearch();
        $indexList = $searchModel->getList($queryParams);

        return $indexList;
	}

	/**
	 * 获取自己曾经收藏的问题
	 *
	 * @return array
	 */
	public function actionMyCollectedQuestions()
	{
		$question_id = QuestionCollection::getQuestionId(Yii::$app->user->id);

		$queryParams = Yii::$app->request->queryParams;
		$queryParams['QuestionCollectedSearch']['id'] = $question_id;

        $searchModel = new QuestionCollectedSearch();
        $indexList = $searchModel->getList($queryParams);

        return $indexList;
	}

	/**
	 * 取消一个自己曾经收藏的问题
	 */
	public function actionCancelMyCollectedQuestions()
	{
		QuestionCollection::deleteCollectedQuestion(Yii::$app->request->post('questionId'));

		return true;
	}
}
