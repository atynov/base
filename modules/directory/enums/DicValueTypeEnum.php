<?php
namespace  modules\directory\enums;

abstract class DicValueTypeEnum
{
    const REGION = 1;
    const DISTRICT = 2;
    const CITY = 3;

    public static function getTypes($language = 'kk')
    {
        $types = [
            self::REGION => [
                'kk' => 'Облыс',
                'ru' => 'Область'
            ],
            self::DISTRICT => [
                'kk' => 'Аудан',
                'ru' => 'Район'
            ],
            self::CITY => [
                'kk' => 'Елді-мекен',
                'ru' => 'Населенный пункт'
            ],
        ];

        return array_map(function ($type) use ($language) {
            return $type[$language] ?? $type['kk'];
        }, $types);
    }
}
