<?php

namespace app\controllers;

use app\models\UploadForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;
use ZipArchive;

class SiteController extends Controller
{
     /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     * @throws \yii\base\InvalidParamException
     */
    public function actionIndex()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $params = array_values(array_filter(explode("\r\n", $model->text)));
            for ($i = 0, $iMax = count($params); $i < $iMax; $i++) {
                if ($i % 3 === 0) {
                    $exifParams[$params[$i]] = ['title' => $params[$i + 1], 'subject' => $params[$i + 1], 'keywords' => $params[$i + 2]];
                }
            }
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            if ($model->upload( $exifParams ?? [])) {
                $files = glob('uploads/*.jpg');
                $zip = new ZipArchive();
                $tmp_file = tempnam('uploads/', '');
                $zip->open($tmp_file, ZipArchive::CREATE);
                foreach ($files as $file) {
                    $download_file = file_get_contents($file);
                    $zip->addFromString(basename($file), $download_file);
                }
                $zip->close();
                return Yii::$app->response->sendFile($tmp_file, 'Result.zip');
            }
        }

        return $this->render('index', ['model' => $model]);
    }
}
