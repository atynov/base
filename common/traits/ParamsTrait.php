<?php
namespace common\traits;

trait ParamsTrait
{

    public static function modelParams()
    {
        return [];
    }

    public function __get($name)
    {
        if (\in_array($name, self::modelParams())
            && !empty($this->paramsJson)
            && array_key_exists($name, $this->paramsJson)) {
            return $this->paramsJson[$name];
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (\in_array($name, self::modelParams())) {
            $this->setParams($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), self::modelParams());
    }
}
