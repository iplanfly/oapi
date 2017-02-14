<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\ForbiddenHttpException;

/**
 * 文件上传模型
 */
class Upload extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    /**
     * @var UploadedFiles
     */
    public $imageFiles;

    const SCENARIO_UPLOAD_FACE = 'face';
    const SCENARIO_UPLOAD_QUESTION_IMAGES = 'questionImages';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'on' => self::SCENARIO_UPLOAD_FACE],
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4, 'on' => self::SCENARIO_UPLOAD_QUESTION_IMAGES],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPLOAD_FACE] = ['imageFile'];
        $scenarios[self::SCENARIO_UPLOAD_QUESTION_IMAGES] = ['imageFiles'];
        return $scenarios;
    }

    /**
     * 上传头像
     *
     * @throws yii\web\ForbiddenHttpException;
     */
    public function uploadFace()
    {
        if ($this->validate()) {
            $path = 'uploads/faces/' . time() . '-' . Yii::$app->user->id . '.' . $this->imageFile->extension;
            $this->imageFile->saveAs($path);
            return $path;
        } else {
            $errors = $this->getFirstErrors();
            throw new ForbiddenHttpException(reset($errors));
        }
    }

    /**
     * 上传问题图片
     * 
     * @param $question_id 问题ID
     * @throws yii\web\ForbiddenHttpException;
     */
    public function uploadQuestionImages($question_id)
    {
        if ($this->validate()) {
            $baseDir = "uploads/questions/$question_id/images/";
            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0777, true);
            }
            $paths = [];
            foreach ($this->imageFiles as $file) {
                $path = $baseDir . $file->baseName . '.' . $file->extension;
                $paths[] = $path;
                $file->saveAs($path);
            }
            return $paths;
        } else {
            $errors = $this->getFirstErrors();
            throw new ForbiddenHttpException(reset($errors));
        }
    }

    /**
     * 自定义获取上传实例。
     *
     * @param $file 上传文件的数组信息
     */
    public function getMyInstance($file)
    {
        $this->imageFile = new UploadedFile;
        $this->imageFile->name = $file['name'];
        $this->imageFile->type = $file['type'];
        $this->imageFile->tempName = $file['tmp_name'];
        $this->imageFile->error = $file['error'];
        $this->imageFile->size = $file['size'];
    }

    /**
     * 自定义获取上传实例。(多个文件)
     */
    public function getMyInstances($files)
    {
        $count = count($files['name']);
        for ($index = 0; $index < $count; $index++) {
            $this->imageFiles[$index] = new UploadedFile;
            $this->imageFiles[$index]->name = $files['name'][$index];
            $this->imageFiles[$index]->type = $files['type'][$index];
            $this->imageFiles[$index]->tempName = $files['tmp_name'][$index];
            $this->imageFiles[$index]->error = $files['error'][$index];
            $this->imageFiles[$index]->size = $files['size'][$index];
        }
    }
}
