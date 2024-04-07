<?php

namespace app\Core\Form;

use app\Core\Model;

class Form
{
    public static function begin($action, $method)
    {
        echo sprintf('<form accept-charset="utf-8" action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attribute)
    {
        return new Field($model, $attribute);
    }
}