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
class Field
{
    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     */
    public const TYPE_TEXT = 'text';

    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     */
    public const TYPE_EMAIL = 'email';

    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     */
    public const TYPE_PASSWORD = 'password';

    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     */
    public const TYPE_NUMBER = 'number';

    /**
     * @var Model $model The model instance to get the attribute and label of the form field.
     */
    public Model $model;

    /**
     * @var string $attribute The attribute of the form field.
     */
    public string $attribute;

    /**
     * @var string $type The type of the form field.
     */
    public string $type;

    /**
     * Field constructor.
     *
     * Initializes the model, attribute, and type of the form field.
     *
     * @param Model $model The model instance to get the attribute and label of the form field.
     * @param string $attribute The attribute of the form field.
     * @param string|Model $type The type of the form field.
     */
    public function __construct(Model $model, string $attribute, Model|string $type = self::TYPE_TEXT)
    {
        $this->type = $type;
        $this->model = $model;
        $this->attribute = $attribute;
    }

    /**
     * Method __toString
     *
     * Renders the form field with the attribute, label, value, and error message.
     *
     * @return string
     */
    public function __toString(): string
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

    /**
     * Sets the field type to password.
     *
     * This method sets the field type to password and returns the current Field object.
     * It allows for method chaining.
     *
     * @return $this The current Field object.
     */
    public function passwordField(): Field
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }
}