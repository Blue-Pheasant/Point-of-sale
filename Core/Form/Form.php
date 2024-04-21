<?php

namespace app\Core\Form;

use app\Core\Model;

/**
 * Class Field
 *
 * This class is responsible for handling the form fields of the application.
 * It uses the Model class to get the attribute and label of the form field.
 *
 * @package app\Core\Form
 */
class Form
{
    /**
     * @param string $action The action attribute of the form.
     * @param string $method The method attribute of the form.
     */
    public static function begin(string $action, string $method): Form
    {
        echo sprintf('<form accept-charset="utf-8" action="%s" method="%s">', $action, $method);
        return new Form();
    }

    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     */
    public static function end(): void
    {
        echo '</form>';
    }

    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     * @param string $attribute The attribute of the form field.
     */
    public function field(Model $model, string $attribute): Field
    {
        return new Field($model, $attribute);
    }
}