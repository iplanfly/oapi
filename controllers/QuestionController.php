<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use app\models\Question;
use yii\web\ForbiddenHttpException;
use app\models\Upload;
use app\models\QuestionSearch;
use app\models\QuestionCollection;
use app\models\User;

/**
 * question 控制器
 *
 * @author piaoyii <sky@piaoyii.com>
 */
class QuestionController extends \yii\rest\Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
	    $behaviors = parent::behaviors();
	    $behaviors['authenticator'] = [
	        'class' => QueryParamAuth::className(),
			'except' => ['index'],
	    ];
	    $behaviors['verbs'] = [
    	    'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['post'],
                'index' => ['get'],
                'collect' => ['post'],
            ],
	    ];
	    return $behaviors;
	}

	/**
	 * 获取问题
	 * 
	 * @return array;
	 */
	public function actionIndex()
	{
        $searchModel = new QuestionSearch();
        $indexList = $searchModel->getList(Yii::$app->request->queryParams);

        if (!empty(($accessToken = Yii::$app->request->get('access-token')))) {
			Yii::$app->user->loginByAccessToken($accessToken);
        }

        if (!Yii::$app->user->isGuest) {
			$indexList = QuestionSearch::attachCollectionStatus($indexList);
        }

        return $indexList;
	}

	/**
	 * 发布问题
	 * 
	 * @throws yii\web\ForbiddenHttpException
	 * @return true|string
	 */
	public function actionCreate()
	{
		$model = new Question;

		$model->attributes = Yii::$app->request->post();
		$model->user_id = Yii::$app->user->id;
		$model->qq_group = Yii::$app->request->post('qqGroup');

		$transaction = Yii::$app->db->beginTransaction();

		if (!$model->save()) {
            $errors = $model->getFirstErrors();
            throw new ForbiddenHttpException(reset($errors));
		}

		if (!empty($_FILES)) {
			$uploadModel = new Upload;
			$uploadModel->getMyInstances($_FILES['images']);
			$model->image = implode("，", $uploadModel->uploadQuestionImages($model->id));
			$model->save();
		}

		$transaction->commit();

		return true;
	}

	/**
	 * 收藏问题
	 *
	 * @throws yii\web\ForbiddenHttpException
	 * @return true
	 */
	public function actionCollect()
	{
		$model = new QuestionCollection;

		foreach (Yii::$app->request->post('questionId') as $questionId) {

			$_model = clone $model;

			$_model->question_id = $questionId;
			$_model->user_id = Yii::$app->user->id;

			if (!$_model->save()) {
	            $errors = $_model->getFirstErrors();
	            throw new ForbiddenHttpException(reset($errors));
			}
		}

		return true;
	}
}
