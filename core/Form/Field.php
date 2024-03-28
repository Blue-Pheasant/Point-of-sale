<?php

namespace app\core\Form;

use app\core\Model;
use app\models\User;

class Field
{
    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';


    public Model $model;
    public string $attribute;
    public string $type;

    public function __construct(Model $model, string $attribute, $type = self::TYPE_TEXT)
    {
        $this->type = $type;
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString()
    {
        return sprintf(
            '
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">%s</label>
                <input type="%s" name="%s" value="%s" class="form-control%s" id="exampleInputEmail1" aria-describedby="emailHelp">
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ',
            $this->model->getLabel($this->attribute),
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->model->getFirstError($this->attribute)
        );
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }
}