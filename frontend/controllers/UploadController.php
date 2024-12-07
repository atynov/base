<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class UploadController extends Controller
{




    public function actionImage()
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
    public function beforeAction($action)
    {
        if ($action->id == 'index') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return self::publicUpload(1);
    }

    public function actionPublicUploadBrowse(){
        /*prd(Yii::$app->request->get());*/
        return self::publicUpload(2);
    }

    private static function publicUpload($by = 1 )
    {
        // $funcNum = $_REQUEST['CKEditorFuncNum'];

        if ($_FILES['upload']) {

            if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name']))) {
                $message = \Yii::t('app', "Please Upload an image.");
            }

            else if ($_FILES['upload']["size"] == 0 OR $_FILES['upload']["size"] > 5*1024*1024)
            {
                $message = \Yii::t('app', "The image should not exceed 5MB.");
            }

            else if ( ($_FILES['upload']["type"] != "image/jpg")
                AND ($_FILES['upload']["type"] != "image/jpeg")
                AND ($_FILES['upload']["type"] != "image/png"))
            {
                $message = \Yii::t('app', "The image type should be JPG , JPEG Or PNG.");
            }

            else if (!is_uploaded_file($_FILES['upload']["tmp_name"])){

                $message = \Yii::t('app', "Upload Error, Please try again.");
            }

            else {
                //you need this (use yii\db\Expression;) for RAND() method
                $random = rand(123456789, 9876543210);

                $extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);

                //Rename the image here the way you want
                $name = date("m-d-Y-h-i-s", time())."-".$random.'.'.$extension;

                // Here is the folder where you will save the images
                $folder = \Yii::$app->basePath.'/../uploads/';

                $url = \Yii::$app->urlManager->createAbsoluteUrl('/uploads/'.$name);

                move_uploaded_file( $_FILES['upload']['tmp_name'], $folder.$name );

            }

            if($by == 1){
                return Json::encode(["fileName"=>$name,"uploaded"=>1,"url" => $url]);
            }else if($by == 2 ){
                return '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("' .$funcNum.'", "'.$url.'", "'.$message.'" );</script>';
            }

        }
    }
}
