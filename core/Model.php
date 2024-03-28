<?php

namespace app\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public const RULE_NUMBER = 'number';
    public const RULE_INVALID_EMAIL = 'invalid email';
    public const RULE_WRONG_PASSWORD = 'wrong password';
    public const RULE_INVALID_ID = 'invalid id';
    public const RULE_MIN_VALUE = 'minint';
    public const RULE_MAX_VALUE = 'maxint';

    abstract public function rules(): array;

    public function getLabel($attribute)
    {
        return '';
    }

    public array $errors = [];

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_MIN_VALUE && (int)$value < $rule['minint']) {
                    $this->addError($attribute, self::RULE_MIN_VALUE, $rule);
                }
                if ($ruleName === self::RULE_MAX_VALUE && (int)$value > $rule['maxint']) {
                    $this->addError($attribute, self::RULE_MAX_VALUE, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttribute = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("
                        SELECT * FROM $tableName WHERE $uniqueAttribute = :$uniqueAttribute;
                    ");
                    $statement->bindValue(":$uniqueAttribute", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addError($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function addError(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errorMessage()
    {
        return [
            self::RULE_UNIQUE => '{field} đã tồn tại.',
            self::RULE_REQUIRED => 'Trường dữ liệu này bắt buộc.',
            self::RULE_EMAIL => 'Trường dữ liệu này phải là email hợp lệ.',
            self::RULE_MIN => 'Ít nhất {min} ký tự.',
            self::RULE_MAX => 'Nhiều nhất {max} ký tự.',
            self::RULE_MIN_VALUE => 'Số lượng ít nhất phải lớn hơn {minint}.',
            self::RULE_MAX_VALUE => 'Số lượng ít nhất phải bé hơn {maxint}.',
            self::RULE_MATCH => 'Trường dữ liệu này phải trùng với {match}.',
            self::RULE_NUMBER => 'Trường dữ liệu này phải là dạng số.',
            self::RULE_INVALID_EMAIL => 'Email chưa được đăng ký.',
            self::RULE_INVALID_ID => 'Người dùng chưa được đăng ký.',
            self::RULE_WRONG_PASSWORD => 'Mật khẩu không chính xác.',
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}