<?php

namespace app\commands;

use Yii;
use app\models\InvitationCode;

/**
 * 邀请码管理
 *
 * @author piaoyii <sky@piaoyii.com>
 */
class InvitationCodeController extends \yii\console\Controller
{
	const EXIT_CODE_GENERATE_ERROR = 2;

	/**
	 * 生成。
	 *
	 * @param $number integer 生成的数量
	 */
	public function actionGenerate($number = 100)
	{
		$invitationCodeDir = Yii::$app->runtimePath . '/invitation-code';
		if (!is_dir($invitationCodeDir)) {
			mkdir($invitationCodeDir);
		}

		$model = new InvitationCode;
		$now = time();

		$transaction = Yii::$app->db->beginTransaction();

		$str = "邀请码{$number}张：\n";

		for ($index = 0; $index < $number; $index++) {
			$_model = clone $model;
			$_model->invitation_code = Yii::$app->security->generateRandomString(8);
			$_model->created_at = $now;
			$_model->created_by = InvitationCode::CREATED_BY_CONSOLE;
			if (!$_model->save()) {
				$transaction->rollback();
				echo "generate fail!\n";
				return self::EXIT_CODE_GENERATE_ERROR;
			}

			$str .= $_model->invitation_code . "\n"; 
		}

		$transaction->commit();

		file_put_contents($invitationCodeDir . '/' . $number . '--' . date("Y-m-d-H-i-s", $now), $str);

		echo "success!\n";
	}
}