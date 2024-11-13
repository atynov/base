<?php
namespace common\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\UploadedFile;
use Yii;

class ImageUploadWidget extends Widget
{
    public $uploadUrl; // URL для обработки загрузки
    public $model;
    public $multiple = false; // Опция для загрузки нескольких изображений
    public $attribute;
    public $submit;
    public $form;

    public $existingImages = []; // Array to hold URLs of existing images
    public function run()
    {
        return $this->render('imageUpload', [
            'uploadUrl' => $this->uploadUrl,
            'model' => $this->model,
            'multiple' => $this->multiple,
            'attribute' => $this->attribute,
            'existingImages' => $this->existingImages, // Pass existing images to view
            'submit' => $this->submit,
            'form' => $this->form,
        ]);
    }
}
