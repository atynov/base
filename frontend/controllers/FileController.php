<?php
namespace frontend\controllers;


use common\components\Controller;
use yii\helpers\Url;
use yii\web\Response;
use Yii;
use yii\web\UploadedFile;

class FileController extends Controller
{
    public function actionUploadImage()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $uploadedFiles = UploadedFile::getInstancesByName('file');
        $uploadedUrls = [];

        foreach ($uploadedFiles as $file) {
            $filename = md5(date('Y-m-d H:i:s') . $file->baseName) . '.' . $file->extension;
            $path = Yii::getAlias('@webroot/uploads/') . $filename;

            if ($file->saveAs($path)) {
                $uploadedUrls[] = Url::to('@web/uploads/' . $filename, true);
            }
        }

        return [
            'success' => true,
            'urls' => $uploadedUrls,
        ];
    }
}