<?php

namespace app\Core;

/**
 * Class FormRequest
 *
 * This class is responsible for handling the form request operations of the application.
 *
 * @package app\Core
 */
abstract class FormRequest extends Request
{
    /**
     * @var array $data The data of the form request.
     */
    protected array $data;

    /**
     * @var array $errors The errors of the form request.
     */
    protected array $errors = [];

    /**
     * @var string RULE_REQUIRED The 'required' validation rule.
     */
    protected const RULE_REQUIRED = 'required';

    /**
     * @var string RULE_EMAIL The 'email' validation rule.
     */
    protected const RULE_EMAIL = 'email';

    /**
     * @var string RULE_MIN The 'min' validation rule.
     */
    protected const RULE_MIN = 'min';

    /**
     * @var string RULE_MAX The 'max' validation rule.
     */
    protected const RULE_MAX = 'max';

    /**
     * @var string RULE_MATCH The 'match' validation rule.
     */
    protected const RULE_MATCH = 'match';

    /**
     * @var string RULE_NUMERIC The 'numeric' validation rule.
     */
    protected const RULE_NUMERIC = 'numeric';

    /**
     * @var string RULE_INTEGER The 'integer' validation rule.
     */
    protected const RULE_INTEGER = 'integer';

    /**
     * @var string RULE_URL The 'url' validation rule.
     */
    protected const RULE_URL = 'url';

    /**
     * @var string RULE_DATE The 'date' validation rule.
     */
    protected const RULE_DATE = 'date';

    /**
     * @var string RULE_TIME The 'time' validation rule.
     */
    protected const RULE_TIME = 'time';

    /**
     * @var string RULE_DATETIME The 'datetime' validation rule.
     */
    protected const RULE_DATETIME = 'datetime';

    /**
     * @var string RULE_DATE_BEFORE The 'date:before' validation rule.
     */
    protected const RULE_DATE_BEFORE = 'date:before';

    /**
     * @var string RULE_DATE_AFTER The 'date:after' validation rule.
     */
    protected const RULE_DATE_AFTER = 'date:after';

    /**
     * @var string RULE_UNIQUE The 'unique' validation rule.
     */
    protected const RULE_UNIQUE = 'unique';

    /**
     * Constructs a new FormRequest object.
     * Merges the request parameters and body and validates the data.
     */
    public function __construct()
    {
        array_merge($this->getPrams(), $this->getBody());
        $this->validate();
    }

    /**
     * Get rules
     *
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * Returns the messages of the form request.
     *
     * @return array The messages of the form request.
     */
    abstract protected function messages(): array;

    /**
     * Returns the labels of the form request.
     *
     * @return array The labels of the form request.
     */
    abstract protected function labels(): array;

    /**
     * Validates the data of the form request.
     *
     * @param string $attribute
     * @return mixed
     */
    protected function getLabel(string $attribute): mixed
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * Validates the data of the form request.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $rules = $this->rules();
    
        foreach ($rules as $field => $rule) {
            foreach ($rule as $r) {
                switch ($r) {
                    case self::RULE_REQUIRED:
                        if (empty($this->data[$field])) {
                            $this->addError($field, self::RULE_REQUIRED, $rules);
                        }
                        break;

                    case self::RULE_EMAIL:
                        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, self::RULE_EMAIL, $rules);
                        }
                        break;

                    case str_contains($r, self::RULE_MIN):
                        $min = explode(':', $r)[1];
                        if (strlen($this->data[$field]) < $min) {
                            $this->addError($field, self::RULE_MIN, $rules);
                        }
                        break;

                    case str_contains($r, self::RULE_MAX):
                        $max = explode(':', $r)[1];
                        if (strlen($this->data[$field]) > $max) {
                            $this->addError($field, self::RULE_MAX, $rules);
                        }
                        break;

                    case str_contains($r, self::RULE_MATCH):
                        $match = explode(':', $r)[1];
                        if ($this->data[$field] !== $this->data[$match]) {
                            $this->addError($field, self::RULE_MATCH, $rules);
                        }
                        break;

                    case self::RULE_NUMERIC:
                        if (!is_numeric($this->data[$field])) {
                            $this->addError($field, self::RULE_NUMERIC, $rules);
                        }
                        break;

                    case self::RULE_INTEGER:
                        if (!is_int($this->data[$field])) {
                            $this->addError($field, self::RULE_INTEGER, $rules);
                        }
                        break;

                    case self::RULE_URL:
                        if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                            $this->addError($field, self::RULE_URL, $rules);
                        }
                        break;

                    case self::RULE_DATE:
                        if (!date_create($this->data[$field])) {
                            $this->addError($field, self::RULE_DATE, $rules);
                        }
                        break;

                    case self::RULE_TIME:
                        if (!date_create_from_format('H:i:s', $this->data[$field])) {
                            $this->addError($field, self::RULE_TIME, $rules);
                        }
                        break;

                    case self::RULE_DATETIME:
                        if (!date_create_from_format('Y-m-d H:i:s', $this->data[$field])) {
                            $this->addError($field, self::RULE_DATETIME, $rules);
                        }
                        break;
            
                    case self::RULE_DATE_BEFORE:
                        $before = explode(':', $r)[1];
                        $date = date_create($this->data[$field]);
                        $beforeDate = date_create($this->data[$before]);
                        if ($date > $beforeDate) {
                            $this->addError($field, self::RULE_DATE_BEFORE, $rules);
                        }
                        break;
                    
                    case self::RULE_DATE_AFTER:
                        $after = explode(':', $r)[1];
                        $date = date_create($this->data[$field]);
                        $afterDate = date_create($this->data[$after]);
                        if ($date < $afterDate) {
                            $this->addError($field, self::RULE_DATE_AFTER, $rules);
                        }
                        break;

                    case str_contains($r, self::RULE_UNIQUE):
                        $unique = explode(':', $r)[1];
                        $model = new $unique();
                        $attr = explode(',', $field);
                        $attrValue = [];
                        foreach ($attr as $a) {
                            $attrValue[$a] = $this->data[$a];
                        }
                        $record = $model::findOne($attrValue);
                        if ($record) {
                            $this->addError($field, self::RULE_UNIQUE, $rules);
                        }
                        break;
                }
            }   
        }
    
        return empty($this->errors);
    }

    /**
     * Returns the error messages of the form request.
     *
     * @return array The error messages of the form request.
     */
    protected function errorsMessage(): array
    {
        return [
            self::RULE_REQUIRED => 'This {label} is required',
            self::RULE_EMAIL => 'This {label} must be a valid email address',
            self::RULE_MIN => 'This {label} must be at least {min} characters',
            self::RULE_MAX => 'This {label} must not exceed {max} characters',
            self::RULE_MATCH => 'This {label} must be the same as {match}',
            self::RULE_NUMERIC => 'This {label} must be a number',
            self::RULE_INTEGER => 'This {label} must be an integer',
            self::RULE_URL => 'This {label} must be a valid URL',
            self::RULE_DATE => 'This {label} must be a valid date',
            self::RULE_TIME => 'This {label} must be a valid time',
            self::RULE_DATETIME => 'This {label} must be a valid date and time',
            self::RULE_DATE_BEFORE => 'This {label} must be before {before}',
            self::RULE_DATE_AFTER => 'This {label} must be after {after}',
            self::RULE_UNIQUE => 'This {label} already exists'
        ];
    }

    /**
     * Adds an error to the form request.
     *
     * @param string $attribute The attribute of the error.
     * @param string $rule The rule of the error.
     * @param array $params The parameters of the error.
     */
    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->errorsMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * Returns the validation errors.
     *
     * This method returns the 'errors' property of the FormRequest object, which is an array of validation errors.
     *
     * @return array The validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
