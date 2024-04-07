<?php

namespace app\Core;

abstract class FormRequest extends Request
{
    protected $data;
    protected $errors = [];

    protected const RULE_REQUIRED = 'required';
    protected const RULE_EMAIL = 'email';
    protected const RULE_MIN = 'min';
    protected const RULE_MAX = 'max';
    protected const RULE_MATCH = 'match';
    protected const RULE_NUMERIC = 'numeric';
    protected const RULE_INTEGER = 'integer';
    protected const RULE_URL = 'url';
    protected const RULE_DATE = 'date';
    protected const RULE_TIME = 'time';
    protected const RULE_DATETIME = 'datetime';
    protected const RULE_DATE_BEFORE = 'date:before';
    protected const RULE_DATE_AFTER = 'date:after';
    protected const RULE_UNIQUE = 'unique';

    public function __construct()
    {
        $this->validate(array_merge($this->getPrams(), $this->getBody()));
    }

    abstract protected function rules(): array;
    abstract protected function messages(): array;
    abstract protected function labels(): array;
    protected function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function validate()
    {
        $rules = $this->rules();
        $messages = $this->messages();
    
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

                    case strpos($r, self::RULE_MIN) !== false:
                        $min = explode(':', $r)[1];
                        if (strlen($this->data[$field]) < $min) {
                            $this->addError($field, self::RULE_MIN, $rules);
                        }
                        break;

                    case strpos($r, self::RULE_MAX) !== false:
                        $max = explode(':', $r)[1];
                        if (strlen($this->data[$field]) > $max) {
                            $this->addError($field, self::RULE_MAX, $rules);
                        }
                        break;

                    case strpos($r, self::RULE_MATCH) !== false:
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

                    case strpos($r, self::RULE_UNIQUE) !== false:
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

    protected function errorsMessage()
    {
        return [
            self::RULE_REQUIRED => 'This {lable} is required',
            self::RULE_EMAIL => 'This {lable} must be a valid email address',
            self::RULE_MIN => 'This {lable} must be at least {min} characters',
            self::RULE_MAX => 'This {lable} must not exceed {max} characters',
            self::RULE_MATCH => 'This {lable} must be the same as {match}',
            self::RULE_NUMERIC => 'This {lable} must be a number',
            self::RULE_INTEGER => 'This {lable} must be an integer',
            self::RULE_URL => 'This {lable} must be a valid URL',
            self::RULE_DATE => 'This {lable} must be a valid date',
            self::RULE_TIME => 'This {lable} must be a valid time',
            self::RULE_DATETIME => 'This {lable} must be a valid date and time',
            self::RULE_DATE_BEFORE => 'This {lable} must be before {before}',
            self::RULE_DATE_AFTER => 'This {lable} must be after {after}',
            self::RULE_UNIQUE => 'This {lable} already exists'
        ];
    }

    public function addError(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorsMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errors()
    {
        return $this->errors;
    }
}
