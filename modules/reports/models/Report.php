<?php

namespace modules\reports\models;

use modules\directory\models\DicValues;
use modules\organization\models\File;
use modules\organization\models\Organization;
use Yii;
use common\components\ActiveRecord;
class Report extends ActiveRecord
{

    public $imageFiles; // Определяем как массив для поддержки множественной загрузки

    public static function tableName()
    {
        return 'reports';
    }

    public function rules()
    {
        return [
            [['direction_id', 'name',  'status', 'direction_id'], 'required'],
            [['start_date', 'end_date', 'people_count', 'link', 'description'], 'safe'],
            [[ 'direction_id', 'period_type', 'month', 'year', 'send_status'], 'integer'],
            ['start_date', 'required', 'when' => fn($model) => in_array($model->status, [1, 2]), 'whenClient' => "function (attribute, value) { return [1, 2].includes($('#report-status').val()); }"],
            ['people_count', 'required', 'when' => fn($model) => in_array($model->status, [1, 2]), 'whenClient' => "function (attribute, value) { return [1, 2].includes($('#report-status').val()); }"],
            ['description', 'required', 'when' => fn($model) => $model->status == 3, 'whenClient' => "function (attribute, value) { return $('#report-status').val() == 3; }"],
            [['imageFiles'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 10],
            [['imageFiles'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $currentUser = Yii::$app->user->identity;
            if ($currentUser) {
                $this->user_id = $this->user_id ?? $currentUser->id;
                $this->organization_id = $this->organization_id ?? $currentUser->organization_id;
            }
        }

        if ($this->period_type == 1) {
            $this->month = null;
        }

        return parent::beforeSave($insert);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }

    public function getFiles()
    {
        return $this->hasMany(File::class, ['target_id' => 'id'])
            ->andWhere(['table' => self::tableName()]);
    }

    public function getDateRange()
    {
        $startDateFormatted = date('d.m.Y', strtotime($this->start_date));
        $endDateFormatted = date('d.m.Y', strtotime($this->end_date));

        if (empty($this->end_date) || $this->end_date == $this->start_date) {
            return $startDateFormatted;
        } else {
            return $startDateFormatted . ' - ' . $endDateFormatted;
        }
    }

    public function getStatusLabel()
    {
        $statuses = [
            1 => 'Орындалуда',
            2 => 'Орындалды',
            3 => 'Орындалмады'
        ];
        return $statuses[$this->status] ?? '';
    }

    public function getDateRangeLabel()
    {
        if (empty($this->end_date) || $this->end_date == $this->start_date) {
            return 'Атқарылған күні';
        } else {
            return 'Атқарылған мерзімі';
        }
    }


    public function getPerLabel()
    {
        if ($this->period_type == 1) {
            return 'Жылдық есеп' ;
        } else {
            return 'Айлық есеп' ;
        }
    }
    public function getPerValue()
    {
        if ($this->period_type == 1) {
            return $this->year ;
        } else {
            $months = [
                1 => 'Қаңтар',
                2 => 'Ақпан',
                3 => 'Наурыз',
                4 => 'Сәуір',
                5 => 'Мамыр',
                6 => 'Маусым',
                7 => 'Шілде',
                8 => 'Тамыз',
                9 => 'Қыркүйек',
                10 => 'Қазан',
                11 => 'Қараша',
                12 => 'Желтоқсан'
            ];
            return $months[$this->month] ?? '';
        }
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'direction_id' => 'Бағыт',
            'month' => 'Айы',
            'year' => 'Жылы',
            'name' => 'Шара атауы',
            'organization_id' => 'Ұйым',
            'status' => 'Статус',
            'user_id' => 'Қолданушы',
            'start_date' => 'Шараның атқарылған мерзімі',
            'end_date' => 'Аяқталу күні',
            'people_count' => 'Қамтылған адам саны',
            'link' => 'Әлеуметтік желідегі немесе сайттағы сілтемесі',
            'description' => 'Сипаттама',
        ];
    }
}